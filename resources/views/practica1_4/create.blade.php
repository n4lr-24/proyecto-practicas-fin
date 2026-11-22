@extends('layouts.app')

@section('titulo', 'Nuevo producto')
@section('encabezado', 'Práctica 1.4 (Nuevo producto)')

@section('contenido')
    <div class="max-w-xl bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('articulos.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="titulo" class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                <input id="titulo" type="text" name="titulo" value="{{ old('titulo') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="detalle" class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                <textarea id="detalle" name="detalle" rows="3" required
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('detalle') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="rubro_id" class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                    <select id="rubro_id" name="rubro_id" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                        <option value="">-- Selecciona --</option>
                        @foreach ($rubros as $rubro)
                            <option value="{{ $rubro->id }}" @selected((string) old('rubro_id') === (string) $rubro->id)>
                                {{ $rubro->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="disponibilidad" class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                    <select id="disponibilidad" name="disponibilidad" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                        <option value="Disponible" @selected(old('disponibilidad', 'Disponible') === 'Disponible')>Disponible</option>
                        <option value="Agotado" @selected(old('disponibilidad') === 'Agotado')>Agotado</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="precio" class="block text-sm font-medium text-slate-700 mb-1">Precio</label>
                    <input id="precio" type="number" name="precio" value="{{ old('precio') }}" step="0.01" min="0.01" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Existencias</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
                </div>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    Guardar
                </button>
                <a href="{{ route('articulos.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
