@extends('layouts.app')

@section('titulo', 'Equipos')
@section('encabezado', 'Práctica 1.5 (Equipos)')

@section('contenido')
    @php
        $esAdmin = auth()->user()->esRol('administrador');
        $esEstudiante = auth()->user()->esRol('estudiante');
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <a href="{{ route('reservas.index') }}"
           class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-100">
            {{ $esAdmin ? 'Ver todas las reservaciones' : 'Mis reservaciones' }}
        </a>

        @if ($esAdmin)
            <a href="{{ route('equipos.create') }}"
               class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                + Nuevo equipo
            </a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">Clave</th>
                        <th class="py-3 px-4">Equipo</th>
                        <th class="py-3 px-4">Sala</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($computadoras as $pc)
                        @php
                            $claseEstado = match ($pc->estado) {
                                'Disponible' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'Reservado' => 'bg-amber-100 text-amber-800 border-amber-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 align-top">
                            <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $pc->clave_equipo }}</td>
                            <td class="py-3 px-4">
                                <p class="font-medium text-slate-900">{{ $pc->etiqueta }}</p>
                                <p class="text-xs text-slate-500">{{ $pc->caracteristicas }}</p>
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $pc->sala }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $claseEstado }}">
                                    {{ $pc->estado }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    @if ($esEstudiante && $pc->estado === 'Disponible')
                                        <form method="POST" action="{{ route('reservas.store') }}" class="flex flex-wrap items-center justify-end gap-1.5">
                                            @csrf
                                            <input type="hidden" name="computadora_id" value="{{ $pc->id }}">
                                            <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" required
                                                   class="rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-slate-900 focus:outline-none">
                                            <input type="time" name="inicio" value="09:00" required
                                                   class="rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-slate-900 focus:outline-none">
                                            <input type="time" name="fin" value="11:00" required
                                                   class="rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-slate-900 focus:outline-none">
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-md bg-slate-900 text-white text-xs font-medium hover:bg-slate-800">
                                                Reservar
                                            </button>
                                        </form>
                                    @elseif ($esEstudiante)
                                        <span class="text-xs text-slate-400">No disponible</span>
                                    @endif

                                    @if ($esAdmin)
                                        <a href="{{ route('equipos.edit', $pc) }}"
                                           class="px-2.5 py-1 rounded-md border border-sky-300 text-xs font-medium text-sky-700 hover:bg-sky-50">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('equipos.destroy', $pc) }}"
                                              onsubmit="return confirm('¿Eliminar el equipo {{ $pc->clave_equipo }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-md border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">No hay equipos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
