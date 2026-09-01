{{--
    phone-mockup partial
    Props:
      $image  – asset URL of the screenshot
      $alt    – alt text
      $width  – pixel width of the phone (controls scale)
      $height – pixel height of the screen area
      $color  – frame color (default dark slate)
--}}
@php
    $w      = $width  ?? 130;
    $h      = $height ?? 260;
    $r      = round($w * 0.165);        // corner radius scales with width
    $border = max(5, round($w * 0.045)); // bezel thickness
    $notchW = round($w * 0.42);
    $notchH = max(14, round($w * 0.11));
    $barW   = round($w * 0.42);
    $col    = $color ?? '#0f172a';

    // Side button positions (decorative)
    $volY1  = round($h * 0.18);
    $volY2  = round($h * 0.26);
    $pwrY   = round($h * 0.20);
@endphp

<div style="
    position: relative;
    width: {{ $w }}px;
    display: inline-block;
">
    {{-- ── Outer shell (main frame) ── --}}
    <div style="
        position: relative;
        width: {{ $w }}px;
        background: {{ $col }};
        border-radius: {{ $r }}px;
        padding: {{ $border }}px;
        box-sizing: border-box;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,0.18),
            inset 0 -1px 0 rgba(0,0,0,0.35),
            inset 1px 0 0 rgba(255,255,255,0.08),
            inset -1px 0 0 rgba(0,0,0,0.25);
    ">

        {{-- Volume buttons (left side) --}}
        <div style="
            position: absolute;
            left: -{{ max(3, round($border * 0.7)) }}px;
            top: {{ $volY1 }}px;
            width: {{ max(3, round($border * 0.7)) }}px;
            height: {{ round($h * 0.07) }}px;
            background: {{ $col }};
            border-radius: {{ max(2, round($border * 0.35)) }}px 0 0 {{ max(2, round($border * 0.35)) }}px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), inset 0 -1px 0 rgba(0,0,0,0.4);
        "></div>
        <div style="
            position: absolute;
            left: -{{ max(3, round($border * 0.7)) }}px;
            top: {{ $volY2 }}px;
            width: {{ max(3, round($border * 0.7)) }}px;
            height: {{ round($h * 0.07) }}px;
            background: {{ $col }};
            border-radius: {{ max(2, round($border * 0.35)) }}px 0 0 {{ max(2, round($border * 0.35)) }}px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), inset 0 -1px 0 rgba(0,0,0,0.4);
        "></div>

        {{-- Power button (right side) --}}
        <div style="
            position: absolute;
            right: -{{ max(3, round($border * 0.7)) }}px;
            top: {{ $pwrY }}px;
            width: {{ max(3, round($border * 0.7)) }}px;
            height: {{ round($h * 0.10) }}px;
            background: {{ $col }};
            border-radius: 0 {{ max(2, round($border * 0.35)) }}px {{ max(2, round($border * 0.35)) }}px 0;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), inset 0 -1px 0 rgba(0,0,0,0.4);
        "></div>

        {{-- ── Inner screen area ── --}}
        <div style="
            position: relative;
            width: 100%;
            border-radius: {{ max(8, $r - $border) }}px;
            overflow: hidden;
            background: #000;
        ">
            {{-- Dynamic Island notch --}}
            <div style="
                position: absolute;
                top: {{ max(6, round($h * 0.018)) }}px;
                left: 50%;
                transform: translateX(-50%);
                width: {{ $notchW }}px;
                height: {{ $notchH }}px;
                background: {{ $col }};
                border-radius: {{ round($notchH * 0.55) }}px;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
            ">
                {{-- Camera dot --}}
                <div style="width:{{ round($notchH * 0.38) }}px; height:{{ round($notchH * 0.38) }}px; border-radius:50%; background:rgba(255,255,255,0.12);"></div>
                {{-- Pill speaker --}}
                <div style="width:{{ round($notchH * 0.62) }}px; height:{{ round($notchH * 0.28) }}px; border-radius:{{ round($notchH * 0.14) }}px; background:rgba(255,255,255,0.08);"></div>
            </div>

            {{-- Screenshot --}}
            <img
                src="{{ $image }}"
                alt="{{ $alt }}"
                style="
                    display: block;
                    width: 100%;
                    height: {{ $h }}px;
                    object-fit: cover;
                    object-position: top;
                "
                loading="lazy"
            >

            {{-- Home indicator bar --}}
            <div style="
                display: flex;
                justify-content: center;
                align-items: center;
                padding: {{ round($h * 0.014) }}px 0;
                background: #000;
            ">
                <div style="
                    width: {{ $barW }}px;
                    height: {{ max(3, round($h * 0.012)) }}px;
                    background: rgba(255,255,255,0.35);
                    border-radius: 3px;
                "></div>
            </div>

            {{-- Glass shine overlay --}}
            <div style="
                position: absolute;
                inset: 0;
                pointer-events: none;
                border-radius: {{ max(8, $r - $border) }}px;
                background: linear-gradient(
                    135deg,
                    rgba(255,255,255,0.12) 0%,
                    rgba(255,255,255,0.04) 30%,
                    transparent 60%
                );
            "></div>
        </div>

        {{-- Top-edge highlight (makes frame look polished metal) --}}
        <div style="
            position: absolute;
            top: 0; left: {{ $r }}px; right: {{ $r }}px;
            height: 1.5px;
            border-radius: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.30), transparent);
            pointer-events: none;
        "></div>
    </div>
</div>
