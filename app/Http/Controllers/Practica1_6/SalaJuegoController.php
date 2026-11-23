<?php

namespace App\Http\Controllers\Practica1_6;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Models\Jugada;
use App\Models\SalaJuego;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalaJuegoController extends Controller
{
    private const MAX_SALAS_ACTIVAS = 3;

    private const MINUTOS_EXPIRACION_ESPERA = 15;

    private const LINEAS_GANADORAS = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6],
    ];

    public function index(Request $request): View
    {
        $this->expirarSalasEnEspera();

        $usuario = $request->user();
        $esAdmin = $usuario->esRol('administrador');

        $jugadoresDisponibles = User::where('rol', RolUsuario::JUGADOR->value)
            ->where('id', '!=', $usuario->id)
            ->orderBy('name')
            ->get();

        $salasDisponibles = SalaJuego::with('retadorX')
            ->where('estado', 'esperando')
            ->whereNull('retador_o_id')
            ->where('retador_x_id', '!=', $usuario->id)
            ->latest()
            ->get();

        $salasActivas = SalaJuego::with(['retadorX', 'retadorO'])
            ->whereIn('estado', ['esperando', 'en_curso'])
            ->when(! $esAdmin, function ($query) use ($usuario) {
                $query->where(function ($sub) use ($usuario) {
                    $sub->where('retador_x_id', $usuario->id)
                        ->orWhere('retador_o_id', $usuario->id);
                });
            })
            ->latest()
            ->get();

        return view('practica1_6.index', compact('jugadoresDisponibles', 'salasDisponibles', 'salasActivas'));
    }

    public function historial(Request $request): View
    {
        $this->expirarSalasEnEspera();

        $usuario = $request->user();
        $esAdmin = $usuario->esRol('administrador');

        $partidas = SalaJuego::with(['retadorX', 'retadorO', 'ganador'])
            ->when(! $esAdmin, function ($query) use ($usuario) {
                $query->where(function ($sub) use ($usuario) {
                    $sub->where('retador_x_id', $usuario->id)
                        ->orWhere('retador_o_id', $usuario->id);
                });
            })
            ->latest()
            ->get();

        return view('practica1_6.historial', compact('partidas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->expirarSalasEnEspera();

        $usuario = $request->user();

        $tieneEnEspera = SalaJuego::where('retador_x_id', $usuario->id)
            ->where('estado', 'esperando')
            ->exists();

        if ($tieneEnEspera) {
            return redirect()->route('gato.index')
                ->with('error', 'Ya tienes una partida en espera de rival.');
        }

        if ($this->contarSalasActivas($usuario->id) >= self::MAX_SALAS_ACTIVAS) {
            return redirect()->route('gato.index')
                ->with('error', 'Alcanzaste el límite máximo de 3 partidas activas simultáneas.');
        }

        $sala = SalaJuego::create([
            'retador_x_id' => $usuario->id,
            'retador_o_id' => null,
            'turno' => 'X',
            'casillas' => array_fill(0, 9, null),
            'estado' => 'esperando',
            'ganador_id' => null,
        ]);

        return redirect()->route('gato.show', $sala)
            ->with('exito', 'Partida creada. Esperando a otro jugador.');
    }

    public function entrar(Request $request, SalaJuego $sala): RedirectResponse
    {
        $this->expirarSalasEnEspera();

        $usuario = $request->user();

        if ($this->contarSalasActivas($usuario->id) >= self::MAX_SALAS_ACTIVAS) {
            return redirect()->route('gato.index')
                ->with('error', 'Alcanzaste el límite máximo de 3 partidas activas simultáneas.');
        }

        $resultado = DB::transaction(function () use ($sala, $usuario) {
            $salaBloqueada = SalaJuego::whereKey($sala->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($salaBloqueada->retador_x_id === $usuario->id) {
                return 'propia';
            }

            if ($salaBloqueada->estado !== 'esperando' || $salaBloqueada->retador_o_id !== null) {
                return 'ocupada';
            }

            $salaBloqueada->update([
                'retador_o_id' => $usuario->id,
                'estado' => 'en_curso',
            ]);

            return 'ok';
        });

        if ($resultado === 'propia') {
            return redirect()->route('gato.show', $sala);
        }

        if ($resultado === 'ocupada') {
            return redirect()->route('gato.index')
                ->with('error', 'La partida ya no está disponible.');
        }

        return redirect()->route('gato.show', $sala)
            ->with('exito', 'Te uniste a la partida como O.');
    }

    public function show(Request $request, SalaJuego $sala): View
    {
        $this->expirarSalasEnEspera();
        $sala->refresh();

        $usuario = $request->user();

        if (! $usuario->esRol('administrador') && ! $sala->esParticipante($usuario->id)) {
            abort(403, 'No tienes permiso para acceder a esta partida.');
        }

        $sala->load(['retadorX', 'retadorO', 'ganador']);

        return view('practica1_6.show', compact('sala'));
    }

    public function estado(Request $request, SalaJuego $sala): JsonResponse
    {
        $this->expirarSalasEnEspera();
        $sala->refresh();

        $usuario = $request->user();

        if (! $usuario->esRol('administrador') && ! $sala->esParticipante($usuario->id)) {
            abort(403, 'No tienes permiso para consultar esta partida.');
        }

        $sala->load(['retadorX', 'retadorO', 'ganador']);
        $miMarca = $sala->marcaDeUsuario($usuario->id);

        return response()->json([
            'id' => $sala->id,
            'estado' => $sala->estado,
            'turno' => $sala->turno,
            'casillas' => $sala->casillas,
            'retador_x' => $sala->retadorX?->name,
            'retador_o' => $sala->retadorO?->name,
            'ganador_id' => $sala->ganador_id,
            'ganador_nombre' => $sala->ganador?->name,
            'mi_marca' => $miMarca,
            'es_mi_turno' => $sala->estado === 'en_curso' && $miMarca !== null && $sala->turno === $miMarca,
        ]);
    }

    public function jugar(Request $request, SalaJuego $sala): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'posicion' => ['required', 'integer', 'between:0,8'],
        ], [
            'posicion.required' => 'Selecciona una casilla.',
            'posicion.integer' => 'Posición inválida.',
            'posicion.between' => 'La casilla debe estar entre 0 y 8.',
        ]);

        $usuario = $request->user();
        $posicion = (int) $datos['posicion'];

        $resultado = DB::transaction(function () use ($sala, $usuario, $posicion) {
            $salaBloqueada = SalaJuego::whereKey($sala->id)
                ->lockForUpdate()
                ->firstOrFail();

            $miMarca = $salaBloqueada->marcaDeUsuario($usuario->id);

            if ($miMarca === null) {
                return ['codigo' => 403, 'error' => 'No formas parte de esta partida.'];
            }

            if ($salaBloqueada->estado !== 'en_curso') {
                return ['codigo' => 422, 'error' => 'La partida no está en curso.'];
            }

            if ($salaBloqueada->turno !== $miMarca) {
                return ['codigo' => 422, 'error' => 'No es tu turno. Espera al otro jugador.'];
            }

            $casillas = $salaBloqueada->casillas;

            if ($casillas[$posicion] !== null) {
                return ['codigo' => 422, 'error' => 'Esa casilla ya está ocupada.'];
            }

            $casillas[$posicion] = $miMarca;

            Jugada::create([
                'sala_id' => $salaBloqueada->id,
                'user_id' => $usuario->id,
                'marca' => $miMarca,
                'posicion' => $posicion,
            ]);

            $hayGanador = $this->verificarGanador($casillas, $miMarca);
            $hayEmpate = ! $hayGanador && ! in_array(null, $casillas, true);

            if ($hayGanador) {
                $salaBloqueada->update([
                    'casillas' => $casillas,
                    'estado' => 'finalizada',
                    'ganador_id' => $usuario->id,
                ]);
            } elseif ($hayEmpate) {
                $salaBloqueada->update([
                    'casillas' => $casillas,
                    'estado' => 'finalizada',
                    'ganador_id' => null,
                ]);
            } else {
                $salaBloqueada->update([
                    'casillas' => $casillas,
                    'turno' => $miMarca === 'X' ? 'O' : 'X',
                ]);
            }

            $salaBloqueada->load(['retadorX', 'retadorO', 'ganador']);

            return ['codigo' => 200, 'sala' => $salaBloqueada, 'mi_marca' => $miMarca];
        });

        if ($resultado['codigo'] !== 200) {
            if ($request->expectsJson()) {
                return response()->json(['mensaje' => $resultado['error']], $resultado['codigo']);
            }

            if ($resultado['codigo'] === 403) {
                abort(403, $resultado['error']);
            }

            return back()->with('error', $resultado['error']);
        }

        $salaActualizada = $resultado['sala'];

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $salaActualizada->id,
                'estado' => $salaActualizada->estado,
                'turno' => $salaActualizada->turno,
                'casillas' => $salaActualizada->casillas,
                'retador_x' => $salaActualizada->retadorX?->name,
                'retador_o' => $salaActualizada->retadorO?->name,
                'ganador_id' => $salaActualizada->ganador_id,
                'ganador_nombre' => $salaActualizada->ganador?->name,
                'mi_marca' => $resultado['mi_marca'],
                'es_mi_turno' => false,
            ]);
        }

        return redirect()->route('gato.show', $salaActualizada);
    }

    public function rendirse(Request $request, SalaJuego $sala): RedirectResponse
    {
        $usuario = $request->user();

        $resultado = DB::transaction(function () use ($sala, $usuario) {
            $salaBloqueada = SalaJuego::whereKey($sala->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $salaBloqueada->esParticipante($usuario->id)) {
                return 'prohibido';
            }

            if (! in_array($salaBloqueada->estado, ['esperando', 'en_curso'], true)) {
                return 'inactiva';
            }

            $ganadorId = null;
            if ($salaBloqueada->estado === 'en_curso') {
                $ganadorId = $salaBloqueada->retador_x_id === $usuario->id
                    ? $salaBloqueada->retador_o_id
                    : $salaBloqueada->retador_x_id;
            }

            $salaBloqueada->update([
                'estado' => 'abandonada',
                'ganador_id' => $ganadorId,
            ]);

            return 'ok';
        });

        if ($resultado === 'prohibido') {
            abort(403, 'No formas parte de esta partida.');
        }

        if ($resultado === 'inactiva') {
            return back()->with('error', 'La partida ya finalizó.');
        }

        return redirect()->route('gato.show', $sala)
            ->with('exito', 'Has abandonado la partida.');
    }

    public function cancelar(SalaJuego $sala): RedirectResponse
    {
        $cancelada = DB::transaction(function () use ($sala) {
            $salaBloqueada = SalaJuego::whereKey($sala->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($salaBloqueada->estado, ['esperando', 'en_curso'], true)) {
                return false;
            }

            $salaBloqueada->update([
                'estado' => 'cancelada',
                'ganador_id' => null,
            ]);

            return true;
        });

        if (! $cancelada) {
            return back()->with('error', 'La partida ya no está activa.');
        }

        return redirect()->route('gato.index')
            ->with('exito', "Partida #{$sala->id} cancelada por el administrador.");
    }

    private function verificarGanador(array $casillas, string $marca): bool
    {
        foreach (self::LINEAS_GANADORAS as [$a, $b, $c]) {
            if ($casillas[$a] === $marca && $casillas[$b] === $marca && $casillas[$c] === $marca) {
                return true;
            }
        }

        return false;
    }

    private function contarSalasActivas(int $userId): int
    {
        return SalaJuego::whereIn('estado', ['esperando', 'en_curso'])
            ->where(function ($query) use ($userId) {
                $query->where('retador_x_id', $userId)
                    ->orWhere('retador_o_id', $userId);
            })
            ->count();
    }

    private function expirarSalasEnEspera(): void
    {
        SalaJuego::where('estado', 'esperando')
            ->where('created_at', '<', now()->subMinutes(self::MINUTOS_EXPIRACION_ESPERA))
            ->update([
                'estado' => 'cancelada',
                'ganador_id' => null,
            ]);
    }
}
