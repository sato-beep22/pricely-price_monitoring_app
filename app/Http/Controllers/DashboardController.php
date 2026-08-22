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
            $latestCeilingIds = \App\Models\CeilingPrice::where('effective_date', '<=', now())
                ->selectRaw('MAX(id) as id')
                ->groupBy('crop_id', 'specification')
                ->get()
                ->pluck('id');

            $ceilings = \App\Models\CeilingPrice::whereIn('id', $latestCeilingIds)->get();
                
            $latestCeilings = [];
            foreach ($ceilings as $ceiling) {
                $key = $ceiling->crop_id . '_' . $ceiling->specification;
                $latestCeilings[$key] = $ceiling;
            }

            return view('dashboard.buyer', compact('latestCeilings'));
        } else {
            $crops = Crop::orderBy('name')->get();

            $latestCeilingIds = \App\Models\CeilingPrice::where('effective_date', '<=', now())
                ->selectRaw('MAX(id) as id')
                ->groupBy('crop_id', 'specification')
                ->pluck('id');

            $ceilingPrices = \App\Models\CeilingPrice::with('crop')->whereIn('id', $latestCeilingIds)->get();

            $sourceLinks = Setting::where('key', 'like', 'da_price_source_link_%')
                ->pluck('value', 'key')
                ->toArray();

            return view('dashboard.farmer', compact('ceilingPrices', 'sourceLinks', 'crops'));
        }
    }
}
