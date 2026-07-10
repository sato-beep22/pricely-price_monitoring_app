<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    /**
     * Show the interactive map to farmers and guests.
     */
    public function index()
    {
        $crops = Crop::all();
        $availableShops = collect(); // Empty collection by default

        // Only show available shops for authenticated farmers
        if (Auth::check() && Auth::user()->isFarmer()) {
            $subscribedBuyerIds = Auth::user()->subscriptions()->pluck('buyer_id')->toArray();
            $availableShops = Shop::where('is_active', true)
                ->whereNotIn('user_id', $subscribedBuyerIds)
                ->get();
        }

        return view('map.index', compact('crops', 'availableShops'));
    }
}
