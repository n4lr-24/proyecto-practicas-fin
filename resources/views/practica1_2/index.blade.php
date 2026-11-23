@extends('layouts.app')

@section('titulo', 'Usuarios')
@section('encabezado', 'Práctica 1.2 (Usuarios)')

@section('contenido')
    @php
        $esAdmin = auth()->user()->esRol('administrador');
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-sm text-slate-600">
                Total de usuarios activos: <strong class="text-slate-900">{{ $usuarios->count() }}</strong>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <input
                type="text"
                id="filtro-usuarios"
                placeholder="Filtrar en tabla..."
                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-slate-900 focus:outline-none"
            >

            @if ($esAdmin)
                <a href="{{ route('usuarios.papelera') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-100">
                    <span>Papelera</span>
                    <span class="px-1.5 py-0.5 text-xs rounded-full bg-slate-200 text-slate-800">{{ $totalEliminados }}</span>
                </a>
            @endif

            <a href="{{ route('usuarios.create') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                + Nuevo Usuario
            </a>
        </div>
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
                        <th class="py-3 px-4">Registro</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios-body" class="divide-y divide-slate-200">
                    @forelse ($usuarios as $item)
                        <tr class="hover:bg-slate-50 fila-usuario">
                            <td class="py-3 px-4 text-slate-500 font-mono text-xs">#{{ $item->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                {{ $item->name }}
                                @if ($item->id === auth()->id())
                                    <span class="ml-1 text-[11px] text-slate-400">(Tú)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->email }}</td>
                            <td class="py-3 px-4">
                                <x-badge-rol :rol="$item->rol" />
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-xs">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('usuarios.show', $item) }}"
                                       class="px-2.5 py-1 rounded-md border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-100">
                                        Ver
                                    </a>
                                    @if ($esAdmin || ! $item->esRol('administrador'))
                                        <a href="{{ route('usuarios.edit', $item) }}"
                                           class="px-2.5 py-1 rounded-md border border-sky-300 text-xs font-medium text-sky-700 hover:bg-sky-50">
                                            Editar
                                        </a>
                                    @endif

                                    @if ($esAdmin && $item->id !== auth()->id())
                                        <form method="POST" action="{{ route('usuarios.destroy', $item) }}"
                                              onsubmit="return confirm('¿Deshabilitar a {{ $item->name }} (Soft Delete)? Podrás restaurarlo desde la Papelera.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-md border border-amber-300 text-xs font-medium text-amber-800 hover:bg-amber-50">
                                                Deshabilitar
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('usuarios.forceDelete', $item->id) }}"
                                              onsubmit="return confirm('¿ELIMINAR PERMANENTEMENTE a {{ $item->name }}? Esta acción NO se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-md border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                                Borrar definitivo
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const inputFiltro = document.getElementById('filtro-usuarios');
    if (inputFiltro) {
        inputFiltro.addEventListener('input', function () {
            const texto = this.value.toLowerCase();
            document.querySelectorAll('.fila-usuario').forEach(fila => {
                fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }
</script>
@endpush
