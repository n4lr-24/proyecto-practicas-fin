@extends('layouts.app')

@section('titulo', 'Papelera')
@section('encabezado', 'Práctica 1.2 (Papelera)')

@section('contenido')
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-slate-600">
            Usuarios deshabilitados: <strong class="text-slate-900">{{ $usuariosEliminados->count() }}</strong>
        </p>
        <a href="{{ route('usuarios.index') }}"
           class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-100">
            &larr; Volver al Directorio
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Correo</th>
                        <th class="py-3 px-4">Rol</th>
                        <th class="py-3 px-4">Deshabilitado el</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($usuariosEliminados as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">#{{ $item->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $item->name }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->email }}</td>
                            <td class="py-3 px-4">
                                <x-badge-rol :rol="$item->rol" />
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">{{ $item->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('usuarios.restaurar', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-md border border-emerald-300 text-xs font-medium text-emerald-700 hover:bg-emerald-50">
                                            Restaurar
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('usuarios.forceDelete', $item->id) }}"
                                          onsubmit="return confirm('¿Eliminar permanentemente a {{ $item->name }}? Esta acción NO se puede revertir.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-md border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                            Borrar definitivo
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                La papelera está vacía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
