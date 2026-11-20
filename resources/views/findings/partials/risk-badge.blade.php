{{-- resources/views/findings/partials/risk-badge.blade.php
     Usage: @include('findings.partials.risk-badge', ['level' => $finding->risk_level])
     Optional: 'size' => 'lg' for a larger pill (used in the detail modal highlight bar) --}}
@php
    $riskMeta = [
        'Extreme' => ['class' => 'extreme', 'icon' => 'fa-solid fa-skull-crossbones'],
        'High'    => ['class' => 'high',    'icon' => 'fa-solid fa-triangle-exclamation'],
        'Medium'  => ['class' => 'medium',  'icon' => 'fa-solid fa-circle-exclamation'],
        'Low'     => ['class' => 'low',     'icon' => 'fa-solid fa-circle-check'],
    ];
    $rm = $riskMeta[$level] ?? ['class' => 'medium', 'icon' => 'fa-solid fa-circle-question'];
@endphp
<span class="risk-pill risk-pill--{{ $rm['class'] }} @if(($size ?? null) === 'lg') risk-pill--lg @endif">
    <i class="{{ $rm['icon'] }}"></i> {{ $level }}
</span>