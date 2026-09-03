@props(['prices' => []])

<div class="card bg-base-100 shadow-sm border border-base-200 mt-4 stat-card">
    <div class="card-body">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <img src="https://img.bomboradyo.com/cauayan/2019/05/DA-LOGO.png" alt="Department of Agriculture Logo" class="w-10 h-10 object-contain drop-shadow-sm rounded-full bg-white border border-base-200">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="card-title text-lg">{{ __('Department of Agriculture Guidelines') }}</h2>
                        <span class="badge badge-warning badge-sm font-semibold">{{ __('Official') }}</span>
                    </div>
                    <p class="text-base-content/60 text-sm mt-0.5">{{ __('Reference prices set by the Department of Agriculture.') }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-base-200 mt-2">
            <table class="table table-zebra" id="ceiling-prices-table">
                <thead class="bg-base-200 text-base-content/80">
                    <tr>
                        <th>{{ __('Crop') }}</th>
                        <th>{{ __('Specification') }}</th>
                        <th>{{ __('Reference Price') }}</th>
                        <th class="hidden md:table-cell">{{ __('Effective Date') }}</th>
                        <th class="hidden lg:table-cell">{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $cp)
                        <tr class="hover">
                            <td class="font-semibold text-base-content">{{ $cp->crop->name }}</td>
                            <td><span class="badge badge-primary badge-outline whitespace-nowrap">{{ ucfirst($cp->specification) }}</span></td>
                            <td>
                                <span class="badge badge-error badge-lg font-bold gap-1 whitespace-nowrap shadow-sm">
                                    ₱{{ number_format($cp->max_price, 2) }}/{{ $cp->crop->unit }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell text-sm">
                                {{ $cp->effective_date->format('M d, Y') }}
                            </td>
                            <td class="hidden lg:table-cell text-sm text-base-content/70 max-w-xs truncate" title="{{ $cp->notes }}">{{ $cp->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center text-base-content/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="italic">{{ __('No reference prices have been set yet.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
