@extends('layouts.app')

@section('titulo', 'Productos')
@section('encabezado', 'Productos')

@section('contenido')
    @php
        $esAdmin = auth()->user()->esRol('administrador');
    @endphp

    <form method="GET" action="{{ route('articulos.index') }}" class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm mb-5 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input
                type="text"
                name="buscar"
                value="{{ request('buscar') }}"
                placeholder="Buscar por nombre..."
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none"
            >

            <select name="rubro_id" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none">
                <option value="">Todas las categorías</option>
                @foreach ($rubros as $rubro)
                    <option value="{{ $rubro->id }}" @selected((string) request('rubro_id') === (string) $rubro->id)>
                        {{ $rubro->titulo }}
                    </option>
                @endforeach
            </select>

            <input
                type="number"
                name="precio_min"
                value="{{ request('precio_min') }}"
                step="0.01"
                min="0"
                placeholder="Precio mín."
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none"
            >

            <input
                type="number"
                name="precio_max"
                value="{{ request('precio_max') }}"
                step="0.01"
                min="0"
                placeholder="Precio máx."
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none"
            >

            <select name="disponibilidad" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none">
                <option value="">Todos los estados</option>
                <option value="Disponible" @selected(request('disponibilidad') === 'Disponible')>Disponible</option>
                <option value="Agotado" @selected(request('disponibilidad') === 'Agotado')>Agotado</option>
            </select>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <div class="flex flex-wrap items-center gap-2">
                <select name="ordenar" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="created_at" @selected($ordenar === 'created_at')>Fecha de registro</option>
                    <option value="titulo" @selected($ordenar === 'titulo')>Nombre</option>
                    <option value="precio" @selected($ordenar === 'precio')>Precio</option>
                    <option value="stock" @selected($ordenar === 'stock')>Existencias</option>
                </select>

                <select name="direccion" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none">
                    <option value="desc" @selected($direccion === 'desc')>Mayor / Reciente / Z-A</option>
                    <option value="asc" @selected($direccion === 'asc')>Menor / Antiguo / A-Z</option>
                </select>

                <select name="por_pagina" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm focus:border-slate-900 focus:outline-none">
                    @foreach ($opcionesPaginacion as $cantidad)
                        <option value="{{ $cantidad }}" @selected($porPagina === $cantidad)>{{ $cantidad }} por página</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    Aplicar
                </button>

                <a href="{{ route('articulos.index') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Limpiar
                </a>
            </div>

            @if ($esAdmin)
                <a href="{{ route('articulos.create') }}"
                   class="px-4 py-1.5 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    + Nuevo producto
                </a>
            @endif
        </div>
    </form>

    <div class="flex items-center justify-between text-xs text-slate-500 mb-3">
        <span>Total encontrados: <strong class="text-slate-800">{{ $articulos->total() }}</strong></span>
        <span>Página <strong class="text-slate-800">{{ $articulos->currentPage() }}</strong> de {{ $articulos->lastPage() }}</span>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Categoría</th>
                        <th class="py-3 px-4">Precio</th>
                        <th class="py-3 px-4">Existencias</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4">Registro</th>
                        @if ($esAdmin)
                            <th class="py-3 px-4 text-right">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($articulos as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">#{{ $item->id }}</td>
                            <td class="py-3 px-4">
                                <p class="font-medium text-slate-900">{{ $item->titulo }}</p>
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $item->detalle }}</p>
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $item->rubro->titulo }}</td>
                            <td class="py-3 px-4 font-mono text-slate-900">${{ number_format((float) $item->precio, 2) }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $item->stock }}</td>
                            <td class="py-3 px-4">
                                <x-badge-disponibilidad :estado="$item->disponibilidad" />
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">{{ $item->created_at?->format('d/m/Y') }}</td>
                            @if ($esAdmin)
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('articulos.edit', $item) }}"
                                           class="px-2.5 py-1 rounded-md border border-sky-300 text-xs font-medium text-sky-700 hover:bg-sky-50">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('articulos.destroy', $item) }}"
                                              onsubmit="return confirm('¿Eliminar {{ $item->titulo }}?');">
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
                            <td colspan="{{ $esAdmin ? 8 : 7 }}" class="py-8 text-center text-slate-500">
                                No se encontraron productos con esos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $articulos->links() }}
    </div>
@endsection
