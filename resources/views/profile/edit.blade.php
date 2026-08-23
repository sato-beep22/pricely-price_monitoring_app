@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight">
            My Profile
        </h2>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
            {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : ($user->isBuyer() ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
            <span class="w-1.5 h-1.5 rounded-full inline-block
                {{ $user->isAdmin() ? 'bg-purple-500' : ($user->isBuyer() ? 'bg-blue-500' : 'bg-emerald-500') }}"></span>
            {{ ucfirst($user->role) }}
        </span>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-24">

    {{-- ── Profile Avatar Card ────────────────────────────────────────────── --}}
    <div class="pricely-card p-6 flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-5 text-center sm:text-left">
        @php
            $initials = collect(explode(' ', $user->name))
                ->map(fn($n) => substr($n, 0, 1))
                ->take(2)
                ->join('');
        @endphp
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm
            {{ $user->isAdmin() ? 'bg-purple-100' : ($user->isBuyer() ? 'bg-blue-100' : 'bg-emerald-100') }}">
            <span class="font-bold text-xl
                {{ $user->isAdmin() ? 'text-purple-700' : ($user->isBuyer() ? 'text-blue-700' : 'text-emerald-700') }}">
                {{ strtoupper($initials) }}
            </span>
        </div>
        <div>
            <p class="text-lg font-bold text-slate-800 leading-tight">{{ $user->name }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $user->email }}</p>
            @if($user->phone)
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                    <i data-lucide="phone" class="w-3 h-3"></i> {{ $user->phone }}
                </p>
            @endif
        </div>
    </div>

    {{-- ── Account Information ─────────────────────────────────────────────── --}}
    <div class="pricely-card p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Account Information</p>
                <p class="text-xs text-slate-400">Update your name, email, and phone number.</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            {{-- Name --}}
            <div class="form-control w-full">
                <label class="label pb-1"><span class="label-text font-semibold text-slate-700">Full Name</span></label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="input input-bordered w-full @error('name') input-error @enderror"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    autocomplete="name"
                />
                @error('name')
                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-control w-full">
                <label class="label pb-1"><span class="label-text font-semibold text-slate-700">Email Address</span></label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                />
                @error('email')
                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="form-control w-full">
                <label class="label pb-1 flex-col items-start sm:flex-row sm:items-center gap-1 sm:gap-0">
                    <span class="label-text font-semibold text-slate-700">Phone Number</span>
                    <span class="label-text-alt text-slate-400 w-full sm:w-auto text-left sm:text-right">Optional — used for SMS alerts</span>
                </label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    class="input input-bordered w-full @error('phone') input-error @enderror"
                    value="{{ old('phone', $user->phone) }}"
                    placeholder="e.g. 09171234567"
                    autocomplete="tel"
                />
                @error('phone')
                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="btn btn-primary btn-sm px-6">
                    Save Changes
                </button>
                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-emerald-600 font-semibold flex items-center gap-1"
                    >
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Saved!
                    </p>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Ceiling Price SMS Alerts (Buyers Only) ────────────────────────── --}}
    @if($user->isBuyer())
    <div class="pricely-card p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                <i data-lucide="bell" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Ceiling Price SMS Alerts</p>
                <p class="text-xs text-slate-400">Get notified by SMS when the government sets a new price ceiling.</p>
            </div>
        </div>

        @if($user->phoneVerified())
            {{-- Verified — show toggle --}}
            <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-4 py-3 rounded-lg border border-emerald-100 mb-4">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Your phone number ({{ $user->phone }}) is verified.</span>
            </div>

            <form method="post" action="{{ route('buyer.sms-notifications.toggle') }}" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-0">
                @csrf
                @method('patch')
                <div>
                    <p class="text-sm font-semibold text-slate-700">SMS Notifications</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $user->smsNotificationsEnabled() ? 'You will receive ceiling price alerts.' : 'You will not receive ceiling price alerts.' }}
                    </p>
                </div>
                <button
                    id="sms-toggle-btn"
                    type="submit"
                    title="{{ $user->smsNotificationsEnabled() ? 'Disable SMS alerts' : 'Enable SMS alerts' }}"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        {{ $user->smsNotificationsEnabled() ? 'bg-blue-600' : 'bg-slate-300' }}"
                >
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                        {{ $user->smsNotificationsEnabled() ? 'translate-x-6' : 'translate-x-1' }}">
                    </span>
                </button>
            </form>

            @if(session('status') === 'sms-enabled')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                   class="mt-3 text-sm text-emerald-600 font-semibold flex items-center gap-1">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i> SMS alerts enabled.
                </p>
            @elseif(session('status') === 'sms-disabled')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                   class="mt-3 text-sm text-slate-500 font-semibold flex items-center gap-1">
                    <i data-lucide="bell-off" class="w-4 h-4"></i> SMS alerts disabled.
                </p>
            @endif

        @elseif($user->phone_verification_code && $user->phone_verification_expires_at && now()->lessThanOrEqualTo($user->phone_verification_expires_at))
            {{-- Code sent — show pending banner + enter button --}}
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-blue-800">
                        A 5-digit code was sent to <strong>{{ $user->phone }}</strong>.
                    </p>
                    <p class="text-xs text-blue-500 mt-0.5">
                        Expires in {{ ceil(now()->floatDiffInMinutes($user->phone_verification_expires_at)) }} minutes.
                    </p>
                </div>
                <button
                    type="button"
                    id="buyer-open-otp-modal"
                    onclick="document.getElementById('buyer-otp-modal').showModal()"
                    class="btn btn-primary bg-blue-600 hover:bg-blue-700 border-none text-white whitespace-nowrap text-sm"
                >
                    Enter Code
                </button>
            </div>

        @else
            {{-- Not verified — show send-code form --}}
            <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 mb-4">
                <p class="text-sm text-slate-600 mb-3">
                    Verify your mobile number to receive ceiling price alert SMS messages.
                </p>
                <form method="post" action="{{ route('buyer.phone.verification.send') }}" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start">
                    @csrf
                    <div class="form-control flex-1">
                        <input
                            type="tel"
                            name="phone"
                            id="buyer-phone-input"
                            class="input input-bordered w-full @error('phone') input-error @enderror"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="e.g. 09171234567"
                            required
                        >
                        @error('phone')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary bg-blue-600 hover:bg-blue-700 border-none text-white">Send Code</button>
                </form>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ── Buyer OTP Modal ──────────────────────────────────────────────────── --}}
    <dialog id="buyer-otp-modal" class="modal">
        <div class="modal-box max-w-sm rounded-2xl shadow-2xl p-0 overflow-hidden">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base">Enter Verification Code</h3>
                        <p class="text-blue-200 text-xs">Check your phone for the 5-digit code</p>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('buyer.phone.verification.verify') }}" class="px-6 py-5">
                @csrf

                @error('code')
                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">
                        <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                        {{ $message }}
                    </div>
                @enderror

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Verification Code</label>
                    <input
                        type="text"
                        name="code"
                        id="buyer-otp-code-input"
                        class="input input-bordered w-full text-center text-2xl font-bold tracking-[0.5em] @error('code') input-error border-red-400 @enderror"
                        placeholder="_ _ _ _ _"
                        maxlength="5"
                        inputmode="numeric"
                        pattern="[0-9]{5}"
                        autocomplete="one-time-code"
                        required
                    >
                    <p class="text-xs text-slate-400 mt-2 text-center">
                        Sent to <span class="font-semibold text-slate-600">{{ $user->phone }}</span>
                    </p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('buyer-otp-modal').close()"
                        class="btn flex-1 bg-slate-100 hover:bg-slate-200 border-none text-slate-600 font-semibold"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="btn flex-1 bg-blue-600 hover:bg-blue-700 border-none text-white font-semibold">
                        Verify
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    @if(session('status') === 'phone-verification-sent' || $errors->has('code'))
        <script>document.addEventListener('DOMContentLoaded', () => document.getElementById('buyer-otp-modal')?.showModal())</script>
    @endif
    @endif

    {{-- ── Phone Verification (Farmers Only) ────────────────────────────────── --}}
    @if($user->isFarmer())
    <div class="pricely-card p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i data-lucide="smartphone" class="w-4 h-4 text-indigo-600"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Phone Verification</p>
                <p class="text-xs text-slate-400">Required to receive SMS alerts for price updates.</p>
            </div>
        </div>

        @if($user->phoneVerified())
            <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-4 py-3 rounded-lg border border-emerald-100">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">Your phone number ({{ $user->phone }}) is verified.</span>
            </div>
        @elseif($user->phone_verification_code && $user->phone_verification_expires_at && now()->lessThanOrEqualTo($user->phone_verification_expires_at))
            {{-- Code sent — show status + button to open modal --}}
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-indigo-800">
                        A 5-digit code was sent to <strong>{{ $user->phone }}</strong>.
                    </p>
                    <p class="text-xs text-indigo-500 mt-0.5">
                        Expires in {{ ceil(now()->floatDiffInMinutes($user->phone_verification_expires_at)) }} minutes.
                    </p>
                </div>
                <button
                    type="button"
                    id="open-otp-modal"
                    onclick="document.getElementById('otp-modal').showModal()"
                    class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 border-none text-white whitespace-nowrap text-sm"
                >
                    Enter Code
                </button>
            </div>
        @else
            {{-- Send Verification Code Form --}}
            <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 mb-4">
                <p class="text-sm text-slate-600 mb-3">
                    Please enter your mobile number to receive a verification code.
                </p>
                <form method="post" action="{{ route('phone.verification.send') }}" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start">
                    @csrf
                    <div class="form-control flex-1">
                        <input
                            type="tel"
                            name="phone"
                            class="input input-bordered w-full @error('phone') input-error @enderror"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="e.g. 09171234567"
                            required
                        >
                        @error('phone')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 border-none text-white">Send Code</button>
                </form>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ── OTP Verification Modal ─────────────────────────────────────────── --}}
    <dialog id="otp-modal" class="modal">
        <div class="modal-box max-w-sm rounded-2xl shadow-2xl p-0 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base">Enter Verification Code</h3>
                        <p class="text-indigo-200 text-xs">Check your phone for the 5-digit code</p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <form method="post" action="{{ route('phone.verification.verify') }}" class="px-6 py-5">
                @csrf

                @error('code')
                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">
                        <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                        {{ $message }}
                    </div>
                @enderror

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Verification Code</label>
                    <input
                        type="text"
                        name="code"
                        id="otp-code-input"
                        class="input input-bordered w-full text-center text-2xl font-bold tracking-[0.5em] @error('code') input-error border-red-400 @enderror"
                        placeholder="_ _ _ _ _"
                        maxlength="5"
                        inputmode="numeric"
                        pattern="[0-9]{5}"
                        autocomplete="one-time-code"
                        required
                    >
                    <p class="text-xs text-slate-400 mt-2 text-center">
                        Sent to <span class="font-semibold text-slate-600">{{ $user->phone }}</span>
                    </p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('otp-modal').close()"
                        class="btn flex-1 bg-slate-100 hover:bg-slate-200 border-none text-slate-600 font-semibold"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="btn flex-1 bg-indigo-600 hover:bg-indigo-700 border-none text-white font-semibold">
                        Verify
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    @if(session('status') === 'phone-verification-sent' || $errors->has('code'))
        <script>document.addEventListener('DOMContentLoaded', () => document.getElementById('otp-modal')?.showModal())</script>
    @endif
    @endif

    {{-- ── PIN Login Setup ─────────────────────────────────────────────────── --}}
    @if(! $user->isAdmin())
    <div class="pricely-card p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i data-lucide="key" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Login PIN</p>
                <p class="text-xs text-slate-400">Set a 4-digit PIN code to log in quickly using your phone number.</p>
            </div>
        </div>

        @include('profile.partials.update-pin-form')
    </div>
    @endif

    {{-- ── Change Password ──────────────────────────────────────────────────── --}}
    <div class="pricely-card p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                <i data-lucide="lock" class="w-4 h-4 text-amber-600"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">Change Password</p>
                <p class="text-xs text-slate-400">Use a long, random password to stay secure.</p>
            </div>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div class="form-control w-full">
                <label class="label pb-1"><span class="label-text font-semibold text-slate-700">Current Password</span></label>
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="input input-bordered w-full @error('current_password', 'updatePassword') input-error @enderror"
                    autocomplete="current-password"
                />
                @error('current_password', 'updatePassword')
                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control w-full">
                <label class="label pb-1"><span class="label-text font-semibold text-slate-700">New Password</span></label>
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="input input-bordered w-full @error('password', 'updatePassword') input-error @enderror"
                    autocomplete="new-password"
                />
                @error('password', 'updatePassword')
                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control w-full">
                <label class="label pb-1"><span class="label-text font-semibold text-slate-700">Confirm New Password</span></label>
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="input input-bordered w-full"
                    autocomplete="new-password"
                />
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="btn btn-warning btn-sm px-6 text-white">
                    Update Password
                </button>
                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-emerald-600 font-semibold flex items-center gap-1"
                    >
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Password updated!
                    </p>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Buyer: Shop Summary ──────────────────────────────────────────────── --}}
    @if($user->isBuyer())
    <div class="pricely-card p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                <i data-lucide="store" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">My Shop</p>
                <p class="text-xs text-slate-400">Your shop's information as shown on the map.</p>
            </div>
        </div>

        @if($user->shop)
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider w-20 flex-shrink-0 pt-0.5">Name</span>
                    <span class="text-sm font-semibold text-slate-800">{{ $user->shop->name }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider w-20 flex-shrink-0 pt-0.5">Address</span>
                    <span class="text-sm text-slate-700">{{ $user->shop->address }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider w-20 flex-shrink-0 pt-0.5">Status</span>
                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full
                        {{ $user->shop->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full inline-block {{ $user->shop->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $user->shop->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100">
                <a href="{{ route('shops.edit') }}" class="btn btn-sm btn-outline border-blue-200 text-blue-600 hover:bg-blue-50 gap-2">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    Edit Shop Profile
                </a>
            </div>
        @else
            <div class="text-center py-6">
                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="store" class="w-6 h-6 text-slate-400"></i>
                </div>
                <p class="text-sm text-slate-500 mb-3">You haven't set up your shop yet.</p>
                <a href="{{ route('shops.edit') }}" class="btn btn-sm btn-primary gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Set Up Shop
                </a>
            </div>
        @endif
    </div>
    @endif

</div>
@endsection
