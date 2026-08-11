@props([
    'status' => session('status'),
    'success' => session('success'),
    'error' => session('error'),
])

@php
    $message = $success ?? $status ?? $error;
    $type = $error ? 'error' : 'success';

    if ($message === 'phone-verification-sent') {
        $message = null; // Suppress global notification; the OTP modal handles this state
    } elseif ($message) {
        $humanMessages = [
            'profile-updated' => 'Your profile information has been updated successfully.',
            'password-updated' => 'Your password has been changed successfully.',
            'verification-link-sent' => 'A new verification link has been sent to your email address.',
            'phone-verified' => 'Your mobile phone number has been verified successfully.',
        ];

        if (array_key_exists($message, $humanMessages)) {
            $message = $humanMessages[$message];
        }
    }
@endphp

@if ($message)
<div
    x-data="{ show: true }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    style="display: flex;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
        @click="show = false"
    ></div>

    <!-- Modal Content Box -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-md p-6 relative z-10 text-center border border-slate-100"
    >
        @if ($type === 'success')
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 ring-8 ring-emerald-50">
                <i data-lucide="check-circle-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Success</h3>
        @else
            <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 ring-8 ring-red-50">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Notice</h3>
        @endif

        <p class="text-slate-600 text-sm leading-relaxed mb-6">
            {{ $message }}
        </p>

        <button
            type="button"
            @click="show = false"
            class="btn w-full {{ $type === 'success' ? 'btn-primary bg-[#04965e] hover:bg-[#037d4e] border-none text-white' : 'btn-error text-white' }} shadow-md"
        >
            OK
        </button>
    </div>
</div>
@endif
