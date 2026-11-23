@extends('layouts.app')

@section('titulo', 'Detalle de usuario')
@section('encabezado', 'Práctica 1.2 (Detalle de usuario)')

@section('contenido')
    @php
        $esAdmin = auth()->user()->esRol('administrador');
    @endphp

    <div class="max-w-xl bg-white border border-slate-200 rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-start justify-between border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $usuario->name }}</h2>
                <p class="text-sm text-slate-500">{{ $usuario->email }}</p>
            </div>
            <x-badge-rol :rol="$usuario->rol" />
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">ID</dt>
                <dd class="text-slate-800 font-mono mt-0.5">#{{ $usuario->id }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Estado</dt>
                <dd class="text-emerald-700 font-medium mt-0.5">Activo</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Fecha de creación</dt>
                <dd class="text-slate-800 mt-0.5">{{ $usuario->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Última actualización</dt>
                <dd class="text-slate-800 mt-0.5">{{ $usuario->updated_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center gap-2">
            @if ($esAdmin || ! $usuario->esRol('administrador'))
                <a href="{{ route('usuarios.edit', $usuario) }}"
                   class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    Editar
                </a>
            @endif

            @if ($esAdmin && $usuario->id !== auth()->id())
                <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                      onsubmit="return confirm('¿Deshabilitar este usuario (Soft Delete)?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg border border-amber-300 text-amber-800 text-sm font-medium hover:bg-amber-50">
                        Deshabilitar (Soft Delete)
                    </button>
                </form>

                <form method="POST" action="{{ route('usuarios.forceDelete', $usuario->id) }}"
                      onsubmit="return confirm('¿ELIMINAR PERMANENTEMENTE este usuario?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-medium hover:bg-rose-50">
                        Eliminar permanente
                    </button>
                </form>
            @endif

            <a href="{{ route('usuarios.index') }}"
               class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-100">
                Volver
            </a>
        </div>
    </div>
@endsection
