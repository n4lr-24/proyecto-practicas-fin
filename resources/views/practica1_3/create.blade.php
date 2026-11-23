@extends('layouts.app')

@section('titulo', 'Nuevo registro')
@section('encabezado', 'Práctica 1.3 (Nuevo registro)')

@section('contenido')
    <div class="max-w-lg bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('calificaciones.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="alumno_user_id" class="block text-sm font-medium text-slate-700 mb-1">Alumno</label>
                <select id="alumno_user_id" name="alumno_user_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="">-- Selecciona un alumno --</option>
                    @foreach ($alumnos as $alumno)
                        <option value="{{ $alumno->id }}" @selected((string) old('alumno_user_id') === (string) $alumno->id)>
                            {{ $alumno->name }} ({{ $alumno->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="unidad" class="block text-sm font-medium text-slate-700 mb-1">Unidad (1 - 4)</label>
                    <select id="unidad" name="unidad" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                        @foreach ([1, 2, 3, 4] as $u)
                            <option value="{{ $u }}" @selected((int) old('unidad', 1) === $u)>Unidad {{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inasistencias" class="block text-sm font-medium text-slate-700 mb-1">Faltas (0 - 5)</label>
                    <input id="inasistencias" type="number" name="inasistencias" value="{{ old('inasistencias', 0) }}" min="0" max="5" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>

                <div>
                    <label for="calificacion" class="block text-sm font-medium text-slate-700 mb-1">Calificación (0 - 10)</label>
                    <input id="calificacion" type="number" name="calificacion" value="{{ old('calificacion') }}" step="0.01" min="0" max="10" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    Guardar
                </button>
                <a href="{{ route('calificaciones.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
