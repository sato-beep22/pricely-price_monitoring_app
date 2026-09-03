@props(['title', 'value', 'desc' => null, 'color' => 'primary', 'stagger' => 1, 'href' => null])

@php
    $colorClass = "text-{$color}";
    $borderClass = "hover:border-{$color}";
    
    $wrapperClasses = "stat bg-base-100 rounded-box shadow-sm border border-base-200 stagger-{$stagger} transition-colors";
    if ($href) {
        $wrapperClasses .= " block {$borderClass}";
    }
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $wrapperClasses }}">
@else
    <div class="{{ $wrapperClasses }}">
@endif

    <div class="stat-figure {{ $colorClass }}">
        {{ $icon ?? '' }}
    </div>
    <div class="stat-title text-base-content/70">{{ $title }}</div>
    <div class="stat-value {{ $colorClass }} font-extrabold">{{ $value }}</div>
    
    @if($desc)
        <div class="stat-desc mt-1">{{ $desc }}</div>
    @endif

@if($href)
    </a>
@else
    </div>
@endif
