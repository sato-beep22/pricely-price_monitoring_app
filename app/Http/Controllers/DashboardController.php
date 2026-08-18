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
            return view('dashboard.buyer');
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
