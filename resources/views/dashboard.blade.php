@extends('layouts.app')

@section('titulo', 'Inicio')
@section('encabezado', 'Inicio')

@section('contenido')
    @php
        $usuario = auth()->user();
    @endphp

    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Hola, {{ $usuario->name }}</h2>
            <p class="text-sm text-slate-500">{{ $usuario->email }}</p>
        </div>
        <x-badge-rol :rol="$usuario->rol" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @if ($usuario->esRol('administrador', 'empleado'))
            <a href="{{ route('usuarios.index') }}" class="block bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:border-slate-400 transition">
                <p class="text-xs text-slate-400">Práctica 1.2</p>
                <h3 class="text-base font-semibold text-slate-900">Usuarios</h3>
            </a>
        @endif

        @if ($usuario->esRol('administrador'))
            <a href="{{ route('usuarios.papelera') }}" class="block bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:border-slate-400 transition">
                <p class="text-xs text-slate-400">Práctica 1.2</p>
                <h3 class="text-base font-semibold text-slate-900">Papelera</h3>
            </a>
        @endif

        @if ($usuario->esRol('profesor', 'alumno'))
            <a href="{{ route('calificaciones.index') }}" class="block bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:border-slate-400 transition">
                <p class="text-xs text-slate-400">Práctica 1.3</p>
                <h3 class="text-base font-semibold text-slate-900">Calificaciones</h3>
            </a>
        @endif

        @if ($usuario->esRol('administrador', 'empleado'))
            <a href="{{ route('articulos.index') }}" class="block bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:border-slate-400 transition">
                <p class="text-xs text-slate-400">Práctica 1.4</p>
                <h3 class="text-base font-semibold text-slate-900">Productos</h3>
            </a>
        @endif
    </div>
@endsection
