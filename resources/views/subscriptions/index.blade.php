@extends('layouts.app')

@section('header')
    <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight page-header">
        {{ __('My Price Alerts') }}
    </h2>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 reveal-stagger-item max-w-7xl mx-auto px-6 md:px-8">

        <!-- Current Subscriptions -->
        <div>
            <div class="pricely-card">
                <div class="p-6">
                    <h2 class="text-xl mb-4 text-slate-800">Active Subscriptions</h2>

                    @if($subscriptions->isEmpty())
                        <div class="text-center py-8 text-base-content/60">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-12 h-12 stroke-current mb-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>You haven't subscribed to any buyers yet.</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach($subscriptions as $sub)
                                <li class="flex items-center justify-between p-4 bg-base-200 rounded-box border border-base-300">
                                    <div>
                                        <div class="font-bold">{{ $sub->buyer->shop->name ?? $sub->buyer->name }}</div>
                                        <div class="text-sm text-base-content/70">{{ $sub->buyer->shop->address ?? 'No address' }}</div>
                                        @if($sub->crop_ids)
                                            <div class="text-xs text-base-content/60 mt-1">
                                                Crops: {{ count($sub->crop_ids) }} selected
                                            </div>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('subscriptions.destroy', $sub) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-error btn-sm btn-outline hover:btn-active">Unsubscribe</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Buyers -->
        <div>
            <div class="pricely-card">
                <div class="p-6">
                    <h2 class="text-xl mb-4 text-slate-800">Available Buyers</h2>

                    <!-- Search Box -->
                    <input
                        type="text"
                        id="shop-search-input"
                        placeholder="Search buyers..."
                        class="input input-bordered input-sm w-full mb-4"
                    />

                    @if($availableShops->isEmpty())
                        <div class="text-center py-8 text-base-content/60">
                            <p>No other active buyers available right now.</p>
                        </div>
                    @else
                        <ul class="space-y-3" id="shops-list">
                            @foreach($availableShops as $shop)
                                <li class="flex items-center justify-between p-4 bg-base-100 hover:bg-base-200 rounded-box border border-base-300 transition-colors shop-item" data-shop-name="{{ $shop->name }}" data-shop-address="{{ $shop->address }}">
                                    <div>
                                        <div class="font-bold">{{ $shop->name }}</div>
                                        <div class="text-sm text-base-content/70">{{ Str::limit($shop->address, 40) }}</div>
                                    </div>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'subscribe-{{ $shop->id }}')"
                                        class="btn btn-primary btn-sm"
                                    >
                                        Subscribe
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Modals rendered outside containers for proper fixed positioning -->
    @foreach($availableShops as $shop)
        <x-subscribe-modal :shop="$shop" :crops="$crops" />
    @endforeach

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('shop-search-input');
                const shopItems = document.querySelectorAll('.shop-item');

                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const query = e.target.value.toLowerCase();

                        shopItems.forEach(item => {
                            const name = item.dataset.shopName.toLowerCase();
                            const address = item.dataset.shopAddress.toLowerCase();

                            if (name.includes(query) || address.includes(query)) {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection
