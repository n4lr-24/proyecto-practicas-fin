@extends('layouts.app')

@section('titulo', 'Calificaciones')
@section('encabezado', 'Práctica 1.3 (Calificaciones)')

@section('contenido')
    @php
        $esProfesor = auth()->user()->esRol('profesor');

        $urlOrden = function (string $campo) use ($ordenar, $direccion, $unidadFiltro) {
            $nuevaDireccion = ($ordenar === $campo && $direccion === 'asc') ? 'desc' : 'asc';

            return route('calificaciones.index', array_filter([
                'ordenar' => $campo,
                'direccion' => $nuevaDireccion,
                'unidad' => $unidadFiltro,
            ], fn ($v) => $v !== null && $v !== ''));
        };

        $flecha = function (string $campo) use ($ordenar, $direccion) {
            if ($ordenar !== $campo) {
                return '';
            }

            return $direccion === 'asc' ? ' ↑' : ' ↓';
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <form method="GET" action="{{ route('calificaciones.index') }}" class="flex flex-wrap items-center gap-2">
            @if ($esProfesor)
                <select name="unidad" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 focus:border-slate-900 focus:outline-none">
                    <option value="">Todas las unidades</option>
                    @foreach ([1, 2, 3, 4] as $u)
                        <option value="{{ $u }}" @selected($unidadFiltro === $u)>Unidad {{ $u }}</option>
                    @endforeach
                </select>
            @endif

            <select name="ordenar" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 focus:border-slate-900 focus:outline-none">
                @if ($esProfesor)
                    <option value="estudiante_nombre" @selected($ordenar === 'estudiante_nombre')>Nombre</option>
                @endif
                <option value="unidad" @selected($ordenar === 'unidad')>Unidad</option>
                <option value="calificacion" @selected($ordenar === 'calificacion')>Calificación</option>
                <option value="inasistencias" @selected($ordenar === 'inasistencias')>Faltas</option>
            </select>

            <select name="direccion" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700 focus:border-slate-900 focus:outline-none">
                <option value="asc" @selected($direccion === 'asc')>{{ $esProfesor ? 'Ascendente (A-Z / Menor a mayor)' : 'Menor a mayor' }}</option>
                <option value="desc" @selected($direccion === 'desc')>{{ $esProfesor ? 'Descendente (Z-A / Mayor a menor)' : 'Mayor a menor' }}</option>
            </select>

            <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-100">
                Ordenar
            </button>
        </form>

        @if ($esProfesor)
            <a href="{{ route('calificaciones.create') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                + Nuevo registro
            </a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">
                            @if ($esProfesor)
                                <a href="{{ $urlOrden('estudiante_nombre') }}" class="hover:text-slate-900">
                                    Alumno{{ $flecha('estudiante_nombre') }}
                                </a>
                            @else
                                Alumno
                            @endif
                        </th>
                        <th class="py-3 px-4">
                            <a href="{{ $urlOrden('unidad') }}" class="hover:text-slate-900">
                                Unidad{{ $flecha('unidad') }}
                            </a>
                        </th>
                        <th class="py-3 px-4">
                            <a href="{{ $urlOrden('inasistencias') }}" class="hover:text-slate-900">
                                Faltas{{ $flecha('inasistencias') }}
                            </a>
                        </th>
                        <th class="py-3 px-4">
                            <a href="{{ $urlOrden('calificacion') }}" class="hover:text-slate-900">
                                Calificación{{ $flecha('calificacion') }}
                            </a>
                        </th>
                        <th class="py-3 px-4">Equivalencia</th>
                        @if ($esProfesor)
                            <th class="py-3 px-4 text-right">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($calificaciones as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $item->estudiante_nombre }}</td>
                            <td class="py-3 px-4 text-slate-700">Unidad {{ $item->unidad }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $item->inasistencias }}</td>
                            <td class="py-3 px-4 font-mono font-medium text-slate-900">{{ number_format((float) $item->calificacion, 2) }}</td>
                            <td class="py-3 px-4">
                                <x-badge-equivalencia :equivalencia="$item->equivalencia" />
                            </td>
                            @if ($esProfesor)
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('calificaciones.edit', $item) }}"
                                           class="px-2.5 py-1 rounded-md border border-sky-300 text-xs font-medium text-sky-700 hover:bg-sky-50">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('calificaciones.destroy', $item) }}"
                                              onsubmit="return confirm('¿Eliminar el registro de {{ $item->estudiante_nombre }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-md border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $esProfesor ? 6 : 5 }}" class="py-8 text-center text-slate-500">
                                No hay registros académicos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
