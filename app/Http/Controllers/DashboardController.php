<?php

namespace App\Http\Controllers;

use App\Models\CeilingPrice;
use App\Models\Crop;
use App\Models\Price;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect users to their respective dashboards based on role.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $latestCeilingIds = CeilingPrice::where('effective_date', '<=', now())
                ->selectRaw('MAX(id) as id')
                ->groupBy('crop_id', 'specification')
                ->pluck('id');

            $ceilingPrices = CeilingPrice::with('crop')->whereIn('id', $latestCeilingIds)->get();

            return view('dashboard.admin', compact('ceilingPrices'));
        } elseif ($user->isBuyer()) {
            $latestCeilingIds = CeilingPrice::where('effective_date', '<=', now())
                ->selectRaw('MAX(id) as id')
                ->groupBy('crop_id', 'specification')
                ->pluck('id');

            $ceilingPrices = CeilingPrice::with('crop')->whereIn('id', $latestCeilingIds)->get();

            $shop = $user->shop;
            $shopLatestPrices = collect();

            if ($shop) {
                $latestPriceIds = Price::where('shop_id', $shop->id)
                    ->selectRaw('MAX(id) as id')
                    ->groupBy('crop_id', 'specification')
                    ->pluck('id');

                $shopLatestPrices = Price::with('crop')->whereIn('id', $latestPriceIds)->get();
            }

            return view('dashboard.buyer', compact('ceilingPrices', 'shopLatestPrices'));
        } else {
            $crops = Crop::orderBy('name')->get();

            $latestCeilingIds = CeilingPrice::where('effective_date', '<=', now())
                ->selectRaw('MAX(id) as id')
                ->groupBy('crop_id', 'specification')
                ->pluck('id');

            $ceilingPrices = CeilingPrice::with('crop')->whereIn('id', $latestCeilingIds)->get();

            $sourceLinks = Setting::where('key', 'like', 'da_price_source_link_%')
                ->pluck('value', 'key')
                ->toArray();

            return view('dashboard.farmer', compact('ceilingPrices', 'sourceLinks', 'crops'));
        }
    }
}
