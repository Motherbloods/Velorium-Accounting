@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-xl shadow-sm border border-slate-200 p-5 ' . $class]) }}>
    {{ $slot }}
</div>
