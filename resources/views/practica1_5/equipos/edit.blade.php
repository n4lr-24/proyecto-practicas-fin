@extends('layouts.app')

@section('titulo', 'Editar equipo')
@section('encabezado', 'Práctica 1.5 (Editar equipo)')

@section('contenido')
    <div class="max-w-xl bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('equipos.update', $computadora) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="clave_equipo" class="block text-sm font-medium text-slate-700 mb-1">Clave del equipo</label>
                    <input id="clave_equipo" type="text" name="clave_equipo" value="{{ old('clave_equipo', $computadora->clave_equipo) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>

                <div>
                    <label for="sala" class="block text-sm font-medium text-slate-700 mb-1">Sala</label>
                    <input id="sala" type="text" name="sala" value="{{ old('sala', $computadora->sala) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="etiqueta" class="block text-sm font-medium text-slate-700 mb-1">Etiqueta / Nombre</label>
                <input id="etiqueta" type="text" name="etiqueta" value="{{ old('etiqueta', $computadora->etiqueta) }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="caracteristicas" class="block text-sm font-medium text-slate-700 mb-1">Características</label>
                <textarea id="caracteristicas" name="caracteristicas" rows="3" required
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('caracteristicas', $computadora->caracteristicas) }}</textarea>
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                <select id="estado" name="estado" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="Disponible" @selected(old('estado', $computadora->estado) === 'Disponible')>Disponible</option>
                    <option value="Reservado" @selected(old('estado', $computadora->estado) === 'Reservado')>Reservado</option>
                    <option value="Mantenimiento" @selected(old('estado', $computadora->estado) === 'Mantenimiento')>Mantenimiento</option>
                </select>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    Actualizar
                </button>
                <a href="{{ route('equipos.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
