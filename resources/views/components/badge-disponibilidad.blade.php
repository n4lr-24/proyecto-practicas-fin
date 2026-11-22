@props(['estado'])

@php
    $clases = $estado === 'Disponible'
        ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
        : 'bg-rose-100 text-rose-800 border-rose-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border $clases"]) }}>
    {{ $estado }}
</span>
