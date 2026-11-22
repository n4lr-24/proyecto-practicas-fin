@props(['equivalencia'])

@php
    $clases = match ($equivalencia) {
        'AU' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'DE' => 'bg-sky-100 text-sky-800 border-sky-200',
        'SA' => 'bg-amber-100 text-amber-800 border-amber-200',
        'NA' => 'bg-rose-100 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $texto = match ($equivalencia) {
        'AU' => 'AU · Autónomo',
        'DE' => 'DE · Destacado',
        'SA' => 'SA · Suficiente',
        'NA' => 'NA · No Acreditado',
        default => $equivalencia,
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border $clases"]) }}>
    {{ $texto }}
</span>
