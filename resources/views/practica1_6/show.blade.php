@extends('layouts.app')

@section('titulo', 'Partida #' . $sala->id)
@section('encabezado', 'Práctica 1.6 (Sala #' . $sala->id . ')')

@section('contenido')
    @php
        $usuario = auth()->user();
        $miMarca = $sala->marcaDeUsuario($usuario->id);
        $esAdmin = $usuario->esRol('administrador');
        $estadoInicial = [
            'estado' => $sala->estado,
            'turno' => $sala->turno,
            'casillas' => $sala->casillas,
            'retador_x' => $sala->retadorX?->name,
            'retador_o' => $sala->retadorO?->name,
            'ganador_id' => $sala->ganador_id,
            'ganador_nombre' => $sala->ganador?->name,
            'mi_marca' => $miMarca,
            'es_mi_turno' => $sala->estado === 'en_curso' && $miMarca !== null && $sala->turno === $miMarca,
        ];
    @endphp

    <div class="max-w-lg mx-auto space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                <div class="rounded-lg border border-slate-200 p-3">
                    <p class="text-xs text-slate-400">Jugador X</p>
                    <p id="jugador-x" class="font-semibold text-slate-900">{{ $sala->retadorX->name }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 p-3">
                    <p class="text-xs text-slate-400">Jugador O</p>
                    <p id="jugador-o" class="font-semibold text-slate-900">{{ $sala->retadorO?->name ?? 'Esperando...' }}</p>
                </div>
            </div>

            <div id="banner-estado" class="rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-medium text-slate-800 mb-4">
                Cargando estado...
            </div>

            <div id="mensaje-jugada" class="hidden mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700 text-center"></div>

            <div id="tablero" class="grid grid-cols-3 gap-2.5 max-w-xs mx-auto">
                @for ($i = 0; $i < 9; $i++)
                    <button
                        type="button"
                        data-posicion="{{ $i }}"
                        class="casilla aspect-square rounded-xl border-2 border-slate-200 bg-slate-50 text-3xl font-bold text-slate-900 flex items-center justify-center hover:bg-slate-100 disabled:cursor-not-allowed transition"
                    >
                        {{ $sala->casillas[$i] ?? '' }}
                    </button>
                @endfor
            </div>

            <div class="mt-5 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('gato.index') }}"
                   class="px-3.5 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-100">
                    &larr; Volver a salas
                </a>

                <div class="flex items-center gap-2">
                    @if ($miMarca && in_array($sala->estado, ['esperando', 'en_curso'], true))
                        <form id="form-rendirse" method="POST" action="{{ route('gato.rendirse', $sala) }}"
                              onsubmit="return confirm('¿Seguro que deseas abandonar esta partida?');">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 rounded-lg border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                Abandonar partida
                            </button>
                        </form>
                    @endif

                    @if ($esAdmin && in_array($sala->estado, ['esperando', 'en_curso'], true))
                        <form id="form-cancelar-admin" method="POST" action="{{ route('gato.cancelar', $sala) }}"
                              onsubmit="return confirm('¿Cancelar esta partida?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-1.5 rounded-lg border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                Cancelar partida
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const urlEstado = @json(route('gato.estado', $sala));
    const urlJugar = @json(route('gato.jugar', $sala));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const elJugadorX = document.getElementById('jugador-x');
    const elJugadorO = document.getElementById('jugador-o');
    const elBanner = document.getElementById('banner-estado');
    const elMensaje = document.getElementById('mensaje-jugada');
    const botones = document.querySelectorAll('.casilla');
    const formRendirse = document.getElementById('form-rendirse');
    const formCancelar = document.getElementById('form-cancelar-admin');

    let estadoActual = @json($estadoInicial);

    let enviando = false;
    let intervalo = null;

    function mostrarError(texto) {
        if (!texto) {
            elMensaje.classList.add('hidden');
            elMensaje.textContent = '';
            return;
        }
        elMensaje.textContent = texto;
        elMensaje.classList.remove('hidden');
    }

    function renderizar(data) {
        estadoActual = data;
        elJugadorX.textContent = data.retador_x || '—';
        elJugadorO.textContent = data.retador_o || 'Esperando...';

        const terminada = ['finalizada', 'abandonada', 'cancelada'].includes(data.estado);

        if (data.estado === 'esperando') {
            elBanner.textContent = 'Esperando a que otro jugador se una...';
            elBanner.className = 'rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-medium text-slate-700 mb-4';
        } else if (data.estado === 'en_curso') {
            if (data.es_mi_turno) {
                elBanner.textContent = `Es tu turno (${data.mi_marca})`;
                elBanner.className = 'rounded-lg bg-emerald-100 px-4 py-2.5 text-center text-sm font-semibold text-emerald-800 mb-4';
            } else if (data.mi_marca) {
                elBanner.textContent = `Turno del rival (${data.turno})`;
                elBanner.className = 'rounded-lg bg-amber-50 px-4 py-2.5 text-center text-sm font-medium text-amber-800 mb-4';
            } else {
                elBanner.textContent = `En curso · Turno de ${data.turno}`;
                elBanner.className = 'rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-medium text-slate-700 mb-4';
            }
        } else if (data.estado === 'finalizada') {
            if (data.ganador_nombre) {
                elBanner.textContent = `Partida finalizada · Ganador: ${data.ganador_nombre}`;
                elBanner.className = 'rounded-lg bg-emerald-100 px-4 py-2.5 text-center text-sm font-semibold text-emerald-900 mb-4';
            } else {
                elBanner.textContent = 'Partida finalizada · Empate';
                elBanner.className = 'rounded-lg bg-amber-100 px-4 py-2.5 text-center text-sm font-semibold text-amber-900 mb-4';
            }
        } else if (data.estado === 'abandonada') {
            elBanner.textContent = data.ganador_nombre
                ? `Partida abandonada · Ganador: ${data.ganador_nombre}`
                : 'Partida abandonada';
            elBanner.className = 'rounded-lg bg-slate-200 px-4 py-2.5 text-center text-sm font-medium text-slate-800 mb-4';
        } else if (data.estado === 'cancelada') {
            elBanner.textContent = 'Partida cancelada por el administrador';
            elBanner.className = 'rounded-lg bg-rose-100 px-4 py-2.5 text-center text-sm font-medium text-rose-800 mb-4';
        }

        botones.forEach((btn, idx) => {
            const valor = data.casillas[idx];
            btn.textContent = valor || '';
            btn.classList.remove('text-indigo-600', 'text-rose-600');
            if (valor === 'X') btn.classList.add('text-indigo-600');
            if (valor === 'O') btn.classList.add('text-rose-600');

            const puedeJugar = !terminada && data.es_mi_turno && valor === null;
            btn.disabled = !puedeJugar || enviando;
        });

        if (terminada) {
            if (formRendirse) formRendirse.classList.add('hidden');
            if (formCancelar) formCancelar.classList.add('hidden');
            if (intervalo) {
                clearInterval(intervalo);
                intervalo = null;
            }
        }
    }

    async function consultarEstado() {
        try {
            const resp = await fetch(urlEstado, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            if (resp.ok) {
                const data = await resp.json();
                renderizar(data);
            }
        } catch (e) {}
    }

    botones.forEach((btn) => {
        btn.addEventListener('click', async () => {
            if (enviando) return;
            const posicion = Number(btn.dataset.posicion);
            enviando = true;
            mostrarError('');

            try {
                const resp = await fetch(urlJugar, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ posicion }),
                });

                const data = await resp.json();
                if (!resp.ok) {
                    mostrarError(data.mensaje || 'No se pudo realizar el movimiento.');
                } else {
                    renderizar(data);
                }
            } catch (e) {
                mostrarError('Error de conexión al enviar el movimiento.');
            } finally {
                enviando = false;
                renderizar(estadoActual);
            }
        });
    });

    renderizar(estadoActual);

    if (!['finalizada', 'abandonada', 'cancelada'].includes(estadoActual.estado)) {
        intervalo = setInterval(consultarEstado, 2000);
    }
</script>
@endpush
