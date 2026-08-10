<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Display the buyer's shop profile.
     */
    public function show()
    {
        $shop = Auth::user()->shop;
        if (! $shop) {
            return redirect()->route('shops.edit')->with('status', 'Please set up your shop profile first.');
        }

        return view('shops.show', compact('shop'));
    }

    /**
     * Show the form for editing the buyer's shop.
     */
    public function edit()
    {
        $shop = Auth::user()->shop ?? new Shop;

        return view('shops.edit', compact('shop'));
    }

    /**
     * Update or create the buyer's shop.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
            'classification' => 'nullable|string|in:trader,miller,wholesaler,retailer,government,cooperative',
        ]);

        $user = Auth::user();

        if ($user->shop) {
            $user->shop->update($validated);
        } else {
            $user->shop()->create($validated);
        }

        return redirect()->route('shops.show')->with('success', 'Shop profile updated successfully.');
    }
}
