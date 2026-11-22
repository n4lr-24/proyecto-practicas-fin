<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-sm bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <div class="mb-5 text-center">
            <h1 class="text-xl font-semibold text-slate-900">Verificación</h1>
            <p class="text-sm text-slate-500 mt-1">
                Ingresa el código enviado a <strong class="text-slate-700">{{ $usuario->email }}</strong>
            </p>
        </div>

        <x-alerta />

        <form method="POST" action="{{ $actionUrl }}" class="space-y-4">
            @csrf

            <div>
                <label for="codigo" class="block text-sm font-medium text-slate-700 mb-1">Código de 6 dígitos</label>
                <input
                    id="codigo"
                    type="text"
                    name="codigo"
                    inputmode="numeric"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    required
                    autofocus
                    placeholder="000000"
                    class="w-full text-center tracking-[0.35em] font-mono text-xl rounded-lg border border-slate-300 px-3 py-2.5 text-slate-900 focus:border-slate-900 focus:outline-none"
                >
            </div>

            <div class="text-xs text-slate-500 flex items-center justify-between">
                <span>Tiempo restante</span>
                <span id="contador-otp" class="font-mono font-medium text-slate-800">--:--</span>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition"
            >
                Verificar
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">
                Volver al login
            </a>
        </div>
    </div>

    <script>
        const expiraMs = {{ $expiraEn->timestamp * 1000 }};
        const elContador = document.getElementById('contador-otp');

        function actualizarContador() {
            const diff = Math.max(0, Math.floor((expiraMs - Date.now()) / 1000));
            const mins = String(Math.floor(diff / 60)).padStart(2, '0');
            const segs = String(diff % 60).padStart(2, '0');
            elContador.textContent = diff > 0 ? `${mins}:${segs}` : 'Expirado';
            if (diff === 0) {
                elContador.classList.add('text-rose-600');
            }
        }

        actualizarContador();
        setInterval(actualizarContador, 1000);
    </script>
</body>
</html>
