@extends('layouts.app')

@section('header')
    <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight page-header">
        {{ __('SMS Logs') }}
    </h2>
    <p class="text-sm text-slate-500 mt-1">Manage and track your SMS message history.</p>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 reveal-stagger-item">
        
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Submitted</h3>
                <div class="text-3xl font-bold text-emerald-600">{{ number_format($stats['submitted']) }}</div>
                <p class="text-xs text-slate-500 mt-2">Messages already handed to the provider.</p>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">In Progress</h3>
                <div class="text-3xl font-bold text-amber-500">{{ number_format($stats['in_progress']) }}</div>
                <p class="text-xs text-slate-500 mt-2">Pending and currently sending messages.</p>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Failed</h3>
                <div class="text-3xl font-bold text-red-500">{{ number_format($stats['failed']) }}</div>
                <p class="text-xs text-slate-500 mt-2">Items that may need a quick resend.</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Message Log</h3>
                <h2 class="text-xl font-bold text-slate-800">Recent sends</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="p-4 pl-6 font-medium">Message Code</th>
                            <th class="p-4 font-medium">Phone Number</th>
                            <th class="p-4 font-medium">Type</th>
                            <th class="p-4 font-medium">Message</th>
                            <th class="p-4 font-medium">Status</th>
                            <th class="p-4 pr-6 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-6 align-top">
                                    <span class="font-mono text-rose-500 bg-rose-50 px-2 py-1 rounded text-xs">{{ $log->message_code ?? 'unknown' }}</span>
                                </td>
                                <td class="p-4 align-top whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-slate-700">
                                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                        {{ $log->phone_number }}
                                    </div>
                                </td>
                                <td class="p-4 align-top whitespace-nowrap">
                                    @if($log->type == 'Price Update')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            <i data-lucide="trending-up" class="w-3 h-3"></i> Price Update
                                        </span>
                                    @elseif($log->type == 'OTP Verification')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                            <i data-lucide="key" class="w-3 h-3"></i> OTP
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $log->type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 align-top">
                                    <div class="text-slate-600 max-w-md line-clamp-2" title="{{ $log->message }}">
                                        {{ $log->message }}
                                    </div>
                                </td>
                                <td class="p-4 align-top whitespace-nowrap">
                                    @if($log->status == 'Completed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i data-lucide="check" class="w-3 h-3"></i> Completed
                                        </span>
                                    @elseif($log->status == 'Failed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                            <i data-lucide="x" class="w-3 h-3"></i> Failed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <i data-lucide="clock" class="w-3 h-3"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 align-top whitespace-nowrap text-slate-500">
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    <span class="text-xs">{{ $log->created_at->format('h:i A') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="message-square-dashed" class="w-12 h-12 text-slate-300 mb-3"></i>
                                        <p>No SMS logs found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
