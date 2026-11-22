@extends('layouts.app')

@section('titulo', 'Nuevo registro')
@section('encabezado', 'Nuevo registro')

@section('contenido')
    <div class="max-w-lg bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('calificaciones.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="alumno_user_id" class="block text-sm font-medium text-slate-700 mb-1">Cuenta de alumno (opcional)</label>
                <select id="alumno_user_id" name="alumno_user_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="">-- Sin cuenta vinculada --</option>
                    @foreach ($alumnos as $alumno)
                        <option value="{{ $alumno->id }}" @selected((string) old('alumno_user_id') === (string) $alumno->id)>
                            {{ $alumno->name }} ({{ $alumno->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estudiante_nombre" class="block text-sm font-medium text-slate-700 mb-1">Nombre del alumno</label>
                <input id="estudiante_nombre" type="text" name="estudiante_nombre" value="{{ old('estudiante_nombre') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="inasistencias" class="block text-sm font-medium text-slate-700 mb-1">Faltas</label>
                    <input id="inasistencias" type="number" name="inasistencias" value="{{ old('inasistencias', 0) }}" min="0" max="100" required
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
