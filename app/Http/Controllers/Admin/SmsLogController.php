<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    /**
     * Display a listing of the SMS logs.
     */
    public function index()
    {
        $logs = SmsLog::orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'submitted' => SmsLog::where('status', 'Completed')->count(),
            'in_progress' => SmsLog::where('status', 'Pending')->count(),
            'failed' => SmsLog::where('status', 'Failed')->count(),
        ];

        return view('admin.sms-logs.index', compact('logs', 'stats'));
    }
}
