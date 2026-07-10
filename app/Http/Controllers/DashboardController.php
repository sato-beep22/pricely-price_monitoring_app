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
            foreach (Crop::all() as $crop) {
                $specs = array_map('trim', explode(',', $crop->specification));
                foreach ($specs as $spec) {
                    $cp = CeilingPrice::where('crop_id', $crop->id)
                        ->where('specification', $spec)
                        ->where('effective_date', '<=', now())
                        ->orderByDesc('effective_date')
                        ->first();
                    if ($cp) {
                        $ceilingPrices->push($cp);
                    }
                }
            }

            return view('dashboard.farmer', compact('ceilingPrices'));
        }
    }
}
