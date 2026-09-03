@extends('layouts.app')

@section('header')
    <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight page-header">
        {{ __('My Price Alerts') }}
    </h2>
@endsection

@section('content')

    <div class="grid grid-cols-1 gap-8 reveal-stagger-item max-w-4xl mx-auto px-6 md:px-8">

        <!-- Current Subscriptions -->
        <div>
            <div class="pricely-card">
                <div class="p-6">
                    <h2 class="text-xl mb-4 text-slate-800">{{ __('Active Subscriptions') }}</h2>

                    @if($subscriptions->isEmpty())
                        <div class="text-center py-8 text-base-content/60">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-12 h-12 stroke-current mb-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>{{ __('You haven\'t subscribed to any buyers yet.') }}</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach($subscriptions as $sub)
                                <li class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-base-200 rounded-box border border-base-300">
                                    <div class="w-full">
                                        <div class="font-bold text-lg leading-tight">{{ $sub->buyer->shop->name ?? $sub->buyer->name }}</div>
                                        <div class="text-sm text-base-content/70 mt-1">{{ $sub->buyer->shop->address ?? 'No address' }}</div>
                                        @if($sub->crop_ids)
                                            <div class="text-xs text-base-content/60 mt-1">
                                                Crops: {{ count($sub->crop_ids) }} selected
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-row items-center justify-between w-full sm:w-auto gap-4">
                                        <form method="POST" action="{{ route('subscriptions.update', $sub) }}" x-data="{ active: {{ $sub->is_active ? 'true' : 'false' }} }">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_active" :value="active ? 1 : 0">
                                            <label class="cursor-pointer flex items-center gap-2 p-0 m-0">
                                                <span class="text-xs font-semibold whitespace-nowrap" :class="active ? 'text-emerald-600' : 'text-slate-400'" x-text="active ? 'SMS ON' : 'SMS OFF'"></span> 
                                                <input type="checkbox" class="toggle toggle-success toggle-sm" x-model="active" @change="$event.target.form.submit()" />
                                            </label>
                                        </form>
                                        <button type="button" @click="$dispatch('open-modal', 'unsubscribe-{{ $sub->id }}')" class="btn btn-error btn-sm btn-outline hover:btn-active whitespace-nowrap">{{ __('Unsubscribe') }}</button>
                                    </div>

                                    <!-- Unsubscribe Confirmation Modal -->
                                    <x-modal name="unsubscribe-{{ $sub->id }}" maxWidth="md">
                                        <div class="p-6 text-center">
                                            <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 ring-8 ring-red-50">
                                                <i data-lucide="bell-off" class="w-8 h-8"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-slate-800 mb-2">{{ __('Unsubscribe Alert') }}</h3>
                                            <p class="text-sm text-slate-600 mb-6">{{ __('Are you sure you want to stop receiving price alert notifications from :name?', ['name' => $sub->buyer->shop->name ?? $sub->buyer->name]) }}</p>
                                            
                                            <div class="flex justify-center gap-3">
                                                <button type="button" @click="$dispatch('close')" class="btn btn-ghost">{{ __('Cancel') }}</button>
                                                <form method="POST" action="{{ route('subscriptions.destroy', $sub) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error text-white">{{ __('Unsubscribe') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </x-modal>
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
                    <h2 class="text-xl mb-4 text-slate-800">{{ __('Available Buyers') }}</h2>

                    <!-- Search Box -->
                    <form method="GET" action="{{ route('subscriptions.index') }}" class="mb-4 flex gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('Search buyers...') }}"
                            class="input input-bordered input-sm w-full"
                        />
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Search') }}</button>
                    </form>

                    @if($availableShops->isEmpty())
                        <div class="text-center py-8 text-base-content/60">
                            <p>{{ __('No other active buyers available right now.') }}</p>
                        </div>
                    @else
                        <ul class="space-y-3" id="shops-list">
                            @foreach($availableShops as $shop)
                                <li class="flex items-center justify-between p-4 bg-base-100 hover:bg-base-200 rounded-box border border-base-300 transition-colors shop-item" data-shop-name="{{ $shop->name }}" data-shop-address="{{ $shop->address }}">
                                    <div>
                                        <div class="font-bold">{{ $shop->name }}</div>
                                        <div class="text-sm text-base-content/70">{{ Str::limit($shop->address, 60) }}</div>
                                        <div class="text-xs font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                            {{ $shop->subscribers_count }} {{ Str::plural('Subscriber', $shop->subscribers_count) }}
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'subscribe-{{ $shop->id }}')"
                                        class="btn btn-primary btn-sm"
                                    >
                                        {{ __('Subscribe') }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-6">
                        {{ $availableShops->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modals rendered outside containers for proper fixed positioning -->
    @foreach($availableShops as $shop)
        <x-subscribe-modal :shop="$shop" :crops="$crops" />
    @endforeach


@endsection
