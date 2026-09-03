<?php

namespace App\Http\Controllers;

use App\Jobs\SendSubscriptionSuccessSms;
use App\Models\Crop;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SemaphoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the subscriptions.
     */
    public function index()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->with('buyer.shop')->get();
        $subscribedBuyerIds = $subscriptions->pluck('buyer_id')->toArray();

        $search = request('search');

        // Get available shops to subscribe to
        $availableShops = Shop::whereNotIn('user_id', $subscribedBuyerIds)
            ->where('is_active', true)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->with('user')
            ->withCount('subscribers')
            ->paginate(5)
            ->withQueryString();

        // Get all available crops
        $crops = Crop::all();

        return view('subscriptions.index', compact('subscriptions', 'availableShops', 'crops'));
    }

    /**
     * Subscribe to a buyer.
     */
    public function store(Request $request, SemaphoreService $smsService)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'crop_ids' => 'required|array|min:1',
            'crop_ids.*' => 'exists:crops,id',
        ]);

        Subscription::create([
            'farmer_id' => Auth::id(),
            'buyer_id' => $request->buyer_id,
            'phone_number' => '+63'.$request->phone_number,
            'crop_ids' => $request->crop_ids,
            'is_active' => true,
        ]);

        $farmer = Auth::user();
        $buyer = User::with('shop')->find($request->buyer_id);
        $shopName = $buyer && $buyer->shop ? $buyer->shop->name : 'the shop';

        if (! empty($farmer->phone)) {
            $message = "Pricely: Matagumpay kang naka-subscribe sa mga update ng presyo mula sa {$shopName}. Makakatanggap ka ng text kapag nagbago ang kanilang presyo.";
            SendSubscriptionSuccessSms::dispatch($farmer->phone, $message);
        }

        return redirect()->back()->with('status', 'Successfully subscribed to price alerts.');
    }

    /**
     * Unsubscribe from a buyer.
     */
    public function update(Request $request, Subscription $subscription)
    {
        if ($subscription->farmer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $subscription->update([
            'is_active' => $request->is_active,
        ]);

        $status = $request->is_active ? 'SMS alerts activated.' : 'SMS alerts paused.';

        return redirect()->back()->with('status', $status);
    }

    /**
     * Unsubscribe from a buyer.
     */
    public function destroy(Subscription $subscription)
    {
        if ($subscription->farmer_id !== Auth::id()) {
            abort(403);
        }

        $subscription->delete();

        return redirect()->back()->with('status', 'Successfully unsubscribed from price alerts.');
    }
}
