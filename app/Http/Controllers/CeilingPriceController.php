<?php

namespace App\Http\Controllers;

use App\Events\CeilingPriceUpdated;
use App\Models\CeilingPrice;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CeilingPriceController extends Controller
{
    /**
     * Display a listing of ceiling prices.
     */
    public function index()
    {
        $ceilingPrices = CeilingPrice::with(['crop', 'admin'])
            ->orderBy('crop_id')
            ->orderBy('specification')
            ->get();

        $crops = Crop::all();

        $latestCeilingIds = CeilingPrice::where('effective_date', '<=', now())
            ->selectRaw('MAX(id) as id')
            ->groupBy('crop_id', 'specification')
            ->get()
            ->pluck('id');

        $latestCeilings = [];
        foreach (CeilingPrice::whereIn('id', $latestCeilingIds)->get() as $ceiling) {
            $key = $ceiling->crop_id.'_'.$ceiling->specification;
            $latestCeilings[$key] = $ceiling;
        }

        return view('admin.ceiling-prices.index', compact('ceilingPrices', 'crops', 'latestCeilings'));
    }

    /**
     * Store one or more ceiling price guidelines in a single batch.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.crop_id' => 'required|exists:crops,id',
            'entries.*.specification' => 'required|string',
            'entries.*.max_price' => 'required|numeric|min:0',
            'entries.*.effective_date' => 'required|date',
            'entries.*.notes' => 'nullable|string',
        ]);

        $updates = [];

        foreach ($request->entries as $entry) {
            $crop = Crop::findOrFail($entry['crop_id']);
            $specification = $entry['specification'];

            // Capture old max price so the SMS can show what changed.
            $existing = CeilingPrice::where('crop_id', $crop->id)
                ->where('specification', $specification)
                ->first();

            $oldMaxPrice = $existing?->max_price;

            CeilingPrice::updateOrCreate(
                [
                    'crop_id' => $crop->id,
                    'specification' => $specification,
                ],
                [
                    'admin_id' => Auth::id(),
                    'max_price' => $entry['max_price'],
                    'effective_date' => $entry['effective_date'],
                    'notes' => $entry['notes'] ?? null,
                ]
            );

            $updates[] = [
                'crop_name' => $crop->name,
                'specification' => $specification !== 'all' ? $specification : null,
                'old_max_price' => $oldMaxPrice,
                'new_max_price' => $entry['max_price'],
            ];
        }

        // Fire one event so subscribers receive a single consolidated SMS.
        CeilingPriceUpdated::dispatch($updates);

        $label = count($updates) === 1 ? '1 ceiling price' : count($updates).' ceiling prices';

        return redirect()->route('admin.ceiling-prices.index')
            ->with('status', "{$label} set successfully.");
    }
}
