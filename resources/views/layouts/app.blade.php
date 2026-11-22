<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen antialiased">
    @php
        $usuarioAuth = auth()->user();
    @endphp

    <div class="min-h-screen flex">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-56 bg-white border-r border-slate-200 transform -translate-x-full md:translate-x-0 md:static md:inset-auto transition-transform duration-200 flex flex-col">
            <div class="h-14 px-5 flex items-center justify-between border-b border-slate-200">
                <a href="{{ route('panel') }}" class="text-sm font-semibold text-slate-900">Panel</a>
                <button type="button" onclick="toggleSidebar()" class="md:hidden text-slate-500 hover:text-slate-700 text-lg">&times;</button>
            </div>

            <nav class="flex-1 p-3 space-y-1 overflow-y-auto text-sm">
                <a href="{{ route('panel') }}"
                   class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('panel') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    Inicio
                </a>

                @if ($usuarioAuth && $usuarioAuth->esRol('administrador', 'empleado'))
                    <a href="{{ route('usuarios.index') }}"
                       class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('usuarios.index', 'usuarios.create', 'usuarios.show', 'usuarios.edit') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Usuarios
                    </a>
                    @if ($usuarioAuth->esRol('administrador'))
                        <a href="{{ route('usuarios.papelera') }}"
                           class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('usuarios.papelera') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            Papelera
                        </a>
                    @endif
                    <a href="{{ route('articulos.index') }}"
                       class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('articulos.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Productos
                    </a>
                @endif

                @if ($usuarioAuth && $usuarioAuth->esRol('profesor', 'alumno'))
                    <a href="{{ route('calificaciones.index') }}"
                       class="block px-3 py-2 rounded-lg font-medium {{ request()->routeIs('calificaciones.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Calificaciones
                    </a>
                @endif
            </nav>
        </aside>

        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-20 bg-slate-900/30 hidden md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-14 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleSidebar()" class="md:hidden px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 text-sm">
                        Menú
                    </button>
                    <h1 class="text-base font-semibold text-slate-900 truncate">
                        @yield('encabezado', 'Inicio')
                    </h1>
                </div>

                @if ($usuarioAuth)
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-sm text-slate-700">{{ $usuarioAuth->name }}</span>
                        <x-badge-rol :rol="$usuarioAuth->rol" />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-100 transition">
                                Salir
                            </button>
                        </form>
                    </div>
                @endif
            </header>

            <main class="flex-1 p-4 sm:p-6 max-w-6xl w-full mx-auto">
                <x-alerta />
                @yield('contenido')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
