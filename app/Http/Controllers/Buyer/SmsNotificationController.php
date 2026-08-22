<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SmsNotificationController extends Controller
{
    /**
     * Toggle the authenticated buyer's SMS notification preference.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->sms_notifications_enabled = ! $user->sms_notifications_enabled;
        $user->save();

        return redirect()->back()->with(
            'status',
            $user->sms_notifications_enabled ? 'sms-enabled' : 'sms-disabled'
        );
    }
}
