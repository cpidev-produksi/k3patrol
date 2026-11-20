{{-- resources/views/findings/partials/status-badge.blade.php
     Usage: @include('findings.partials.status-badge', ['finding' => $f]) --}}
@php
    $statusMeta = [
        'Open'             => ['class' => 'open',           'icon' => 'fa-solid fa-folder-open'],
        'On Process'       => ['class' => 'on-process',     'icon' => 'fa-solid fa-person-digging'],
        'Closed'           => ['class' => 'closed',         'icon' => 'fa-solid fa-circle-check'],
        'Closed Verified'  => ['class' => 'closed-verified','icon' => 'fa-solid fa-shield-halved'],
    ];
    $sm = $statusMeta[$finding->status] ?? ['class' => 'closed', 'icon' => 'fa-solid fa-circle-question'];
    $isOverdue = $finding->isOverdue();
@endphp
<span class="status-pill {{ $isOverdue ? 'status-pill--overdue' : 'status-pill--' . $sm['class'] }}">
    <i class="{{ $isOverdue ? 'fa-solid fa-triangle-exclamation' : $sm['icon'] }}"></i>
    {{ $isOverdue ? 'Overdue' : $finding->status }}
</span>