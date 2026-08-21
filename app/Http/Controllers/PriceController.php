<?php

namespace App\Http\Controllers;

use App\Events\PriceUpdated;
use App\Models\Crop;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
    /**
     * Show the form for recording a new price.
     */
    public function create()
    {
        $shop = Auth::user()->shop;

        if (! $shop) {
            return redirect()->route('shops.edit')->with('error', 'Please set up your shop profile first.');
        }

        $crops = Crop::all();

        // Get the absolute latest price IDs for this shop per crop/spec
        $latestPriceIds = Price::where('shop_id', $shop->id)
            ->selectRaw('MAX(id) as id')
            ->groupBy('crop_id', 'specification')
            ->pluck('id');

        // Fetch those latest prices in one go
        $prices = Price::whereIn('id', $latestPriceIds)->get();

        $latestPrices = [];
        foreach ($prices as $price) {
            $latestPrices[$price->crop_id][$price->specification] = $price;
        }

        $ceilings = \App\Models\CeilingPrice::where('effective_date', '<=', now())
            ->orderByDesc('effective_date')
            ->get();
            
        $latestCeilings = [];
        foreach ($ceilings as $ceiling) {
            $key = $ceiling->crop_id . '_' . $ceiling->specification;
            if (!isset($latestCeilings[$key])) {
                $latestCeilings[$key] = $ceiling;
            }
        }

        return view('prices.create', compact('crops', 'latestPrices', 'latestCeilings'));
    }

    /**
     * Store one or more recorded prices.
     */
    public function store(Request $request)
    {
        $shop = Auth::user()->shop;

        if (! $shop) {
            return redirect()->route('shops.edit')->with('error', 'Please set up your shop profile first.');
        }

        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.crop_id' => 'required|exists:crops,id',
            'entries.*.specification' => 'required|string',
            'entries.*.price_per_kg' => 'required|numeric|min:0',
        ]);

        $ceilings = \App\Models\CeilingPrice::where('effective_date', '<=', now())
            ->orderByDesc('effective_date')
            ->get();
            
        $latestCeilings = [];
        foreach ($ceilings as $ceiling) {
            $key = $ceiling->crop_id . '_' . $ceiling->specification;
            if (!isset($latestCeilings[$key])) {
                $latestCeilings[$key] = $ceiling;
            }
        }

        $errors = [];
        foreach ($request->entries as $key => $entry) {
            $ceilingKey = $entry['crop_id'] . '_' . $entry['specification'];
            if (isset($latestCeilings[$ceilingKey])) {
                $ceiling = $latestCeilings[$ceilingKey];
                if ($entry['price_per_kg'] < $ceiling->max_price) {
                    $crop = Crop::find($entry['crop_id']);
                    $errors["entries.{$key}.price_per_kg"] = "For {$crop->name} ({$entry['specification']}), your price should exceed the minimum price of DA (₱" . number_format($ceiling->max_price, 2) . ").";
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $count = 0;

        $updates = [];

        foreach ($request->entries as $entry) {
            $crop = Crop::findOrFail($entry['crop_id']);
            $specification = $entry['specification'];

            // Get the old price BEFORE inserting the new one
            $oldPriceRecord = Price::where('shop_id', $shop->id)
                ->where('crop_id', $crop->id)
                ->where('specification', $specification)
                ->latest('recorded_at')
                ->first();

            $price = Price::create([
                'shop_id' => $shop->id,
                'crop_id' => $crop->id,
                'specification' => $specification,
                'price_per_kg' => $entry['price_per_kg'],
                'recorded_at' => Carbon::now(),
                'source' => 'buyer',
            ]);

            $updates[] = [
                'crop_id' => $crop->id,
                'crop_name' => $crop->name,
                'specification' => $specification,
                'old_price' => $oldPriceRecord ? $oldPriceRecord->price_per_kg : null,
                'new_price' => $price->price_per_kg,
            ];
            $count++;
        }

        if (! empty($updates)) {
            // Dispatch one event for all updates
            PriceUpdated::dispatch($shop, $updates);
        }

        $label = $count === 1 ? '1 crop price' : "{$count} crop prices";

        return redirect()->route('prices.create')->with('status', "{$label} updated successfully. Alerts sent to subscribed farmers.");
    }
}
