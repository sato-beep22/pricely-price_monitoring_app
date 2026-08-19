<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhoneVerificationRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PhoneVerificationController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct(public SemaphoreService $smsService) {}

    /**
     * Send the verification code to the user's phone.
     */
    public function store(PhoneVerificationRequest $request)
    {
        $user = $request->user();

        // Check if there's already a valid code sent recently (e.g., within 1 minute) to prevent spam
        // (Assuming you have a way to track this, otherwise simplified here)

        // Generate a 5-digit verification code
        $code = str_pad((string) random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        // Save user's new phone and hash of the code + expiry
        $user->phone = $request->phone;
        $user->phone_verification_code = Hash::make($code);
        $user->phone_verification_expires_at = now()->addMinutes(5);
        $user->save();

        // Send OTP via SMS
        $sent = $this->smsService->sendOtp($user->phone, $code);

        if (! $sent) {
            Log::warning("OTP failed for phone {$user->phone}. Code: {$code}");
        }

        return redirect()->back()->with('status', 'phone-verification-sent');
    }

    /**
     * Verify the code submitted by the user.
     */
    public function verify(VerifyCodeRequest $request)
    {
        $user = $request->user();

        // Check if code has expired
        if (! $user->phone_verification_expires_at || now()->greaterThan($user->phone_verification_expires_at)) {
            return back()->withErrors(['code' => 'The verification code has expired. Please request a new one.']);
        }

        // Check if code matches
        if (! Hash::check($request->code, $user->phone_verification_code)) {
            return back()->withErrors(['code' => 'The verification code is incorrect.']);
        }

        // Verification successful
        $user->phone_verified_at = now();
        $user->phone_verification_code = null;
        $user->phone_verification_expires_at = null;
        $user->save();

        return redirect()->back()->with('status', 'phone-verified');
    }
}
