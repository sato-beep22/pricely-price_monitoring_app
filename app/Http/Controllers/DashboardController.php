<?php

namespace App\Http\Controllers;

use App\Models\CeilingPrice;
use App\Models\Crop;
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
            return view('dashboard.admin');
        } elseif ($user->isBuyer()) {
            return view('dashboard.buyer');
        } else {
            $ceilingPrices = collect();
            $crops = Crop::orderBy('name')->get();
            foreach ($crops as $crop) {
                $specs = array_map('trim', explode(',', $crop->specification));
                foreach ($specs as $spec) {
                    $cp = CeilingPrice::where('crop_id', $crop->id)
                        ->where('specification', $spec)
                        ->first();
                    if ($cp) {
                        $ceilingPrices->push($cp);
                    }
                }
            }

            $sourceLinks = \App\Models\Setting::where('key', 'like', 'da_price_source_link_%')
                ->pluck('value', 'key')
                ->toArray();
            return view('dashboard.farmer', compact('ceilingPrices', 'sourceLinks', 'crops'));
        }
    }
}
