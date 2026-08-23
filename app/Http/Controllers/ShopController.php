<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Auto-activate the shop when a valid location has been set
        if ((float) $validated['latitude'] !== 0.0 || (float) $validated['longitude'] !== 0.0) {
            $validated['is_active'] = true;
        }

        // Handle photo removal request
        if (! empty($validated['remove_photo']) && $user->shop && $user->shop->photo_path) {
            Storage::disk('public')->delete($user->shop->photo_path);
            $validated['photo_path'] = null;
        }

        // Handle new photo upload (overrides removal if both are somehow sent)
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists to avoid orphaned files
            if ($user->shop && $user->shop->photo_path) {
                Storage::disk('public')->delete($user->shop->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('shop_photos', 'public');
        }

        // Remove raw keys so they don't get passed to the model
        unset($validated['photo'], $validated['remove_photo']);

        if ($user->shop) {
            $user->shop->update($validated);
        } else {
            $user->shop()->create($validated);
        }

        return redirect()->route('shops.show')->with('success', 'Shop profile updated successfully.');
    }
}
