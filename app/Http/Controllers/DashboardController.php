<?php

namespace App\Http\Controllers;

use App\Models\CeilingPrice;
use App\Models\Crop;
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
            return view('dashboard.admin');
        } elseif ($user->isBuyer()) {
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

            return view('dashboard.buyer', compact('latestCeilings'));
        } else {
            $crops = Crop::orderBy('name')->get();
            $ceilingPrices = CeilingPrice::with('crop')->get();

            $sourceLinks = Setting::where('key', 'like', 'da_price_source_link_%')
                ->pluck('value', 'key')
                ->toArray();

            return view('dashboard.farmer', compact('ceilingPrices', 'sourceLinks', 'crops'));
        }
    }
}
