@props(['status' => 'draft'])

@php
    $map = [
        'draft' => 'bg-amber-100 text-amber-700',
        'submitted' => 'bg-blue-100 text-accent',
        'approved' => 'bg-blue-100 text-accent',
        'posted' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-red-100 text-error',
        'lunas' => 'bg-emerald-100 text-emerald-700',
        'belum_lunas' => 'bg-amber-100 text-amber-700',
        'overdue' => 'bg-red-100 text-error',
    ];
    $class = $map[$status] ?? 'bg-slate-100 text-slate-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-block px-2 py-0.5 rounded-full text-xs font-medium ' . $class]) }}>
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
