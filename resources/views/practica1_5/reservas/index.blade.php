@extends('layouts.app')

@section('titulo', 'Reservaciones')
@section('encabezado', 'Práctica 1.5 (Reservaciones)')

@section('contenido')
    @php
        $esAdmin = auth()->user()->esRol('administrador');
    @endphp

    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-slate-600">
            Total: <strong class="text-slate-900">{{ $reservas->count() }}</strong>
        </p>
        <a href="{{ route('equipos.index') }}"
           class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-100">
            Ver equipos
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        @if ($esAdmin)
                            <th class="py-3 px-4">Usuario</th>
                        @endif
                        <th class="py-3 px-4">Equipo</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Horario</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($reservas as $reserva)
                        @php
                            $clase = match ($reserva->estado) {
                                'Activa' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'Cancelada' => 'bg-rose-100 text-rose-800 border-rose-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">#{{ $reserva->id }}</td>
                            @if ($esAdmin)
                                <td class="py-3 px-4">
                                    <p class="font-medium text-slate-900">{{ $reserva->usuario->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $reserva->usuario->email }}</p>
                                </td>
                            @endif
                            <td class="py-3 px-4">
                                <p class="font-medium text-slate-900">{{ $reserva->computadora->clave_equipo }} · {{ $reserva->computadora->etiqueta }}</p>
                                <p class="text-xs text-slate-500">{{ $reserva->computadora->sala }}</p>
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $reserva->fecha->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-700">{{ substr($reserva->inicio, 0, 5) }} - {{ substr($reserva->fin, 0, 5) }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $clase }}">
                                    {{ $reserva->estado }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('reservas.show', $reserva) }}"
                                       class="px-2.5 py-1 rounded-md border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-100">
                                        Ver
                                    </a>
                                    @if ($reserva->estado === 'Activa')
                                        <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}"
                                              onsubmit="return confirm('¿Cancelar esta reservación?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-md border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                                Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $esAdmin ? 7 : 6 }}" class="py-8 text-center text-slate-500">
                                No hay reservaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
