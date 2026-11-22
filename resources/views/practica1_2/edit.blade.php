@extends('layouts.app')

@section('titulo', 'Editar usuario')
@section('encabezado', 'Editar usuario')

@section('contenido')
    <div class="max-w-xl bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nombre completo</label>
                <input id="name" type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                       class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                       class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-slate-900 focus:outline-none">
            </div>

            <div>
                <label for="rol" class="block text-sm font-medium text-slate-700 mb-1">Rol</label>
                <select id="rol" name="rol" required
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm bg-white focus:border-slate-900 focus:outline-none">
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->value }}" @selected(old('rol', $usuario->rol->value) === $rol->value)>
                            {{ $rol->etiqueta() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 border-t border-slate-200">
                <p class="text-xs text-slate-500 mb-3">Deja los campos de contraseña vacíos si no deseas cambiarla.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Nueva contraseña</label>
                        <input id="password" type="password" name="password" minlength="8"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmar contraseña</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" minlength="8"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-slate-900 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    Actualizar Usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
