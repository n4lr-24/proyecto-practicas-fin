@props(['rol'])

@php
    $valor = $rol instanceof \App\Enums\RolUsuario ? $rol->value : (string) $rol;
    $etiqueta = $rol instanceof \App\Enums\RolUsuario ? $rol->etiqueta() : ucfirst($valor);

    $clases = match ($valor) {
        'administrador' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'empleado' => 'bg-sky-100 text-sky-800 border-sky-200',
        'profesor' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'alumno' => 'bg-amber-100 text-amber-800 border-amber-200',
        'estudiante' => 'bg-violet-100 text-violet-800 border-violet-200',
        'jugador' => 'bg-rose-100 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border $clases"]) }}>
    {{ $etiqueta }}
</span>
