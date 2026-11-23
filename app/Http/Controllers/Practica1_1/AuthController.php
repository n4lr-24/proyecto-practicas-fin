<?php

namespace App\Http\Controllers\Practica1_1;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Mail\Practica1_1\CodigoAdminMail;
use App\Models\CodigoOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    private const MAX_INTENTOS_OTP = 5;

    private const MINUTOS_BLOQUEO_OTP = 5;

    public function mostrarLogin(): View
    {
        return view('practica1_1.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        $usuario = User::where('email', $credenciales['email'])->first();

        if (! $usuario || ! Hash::check($credenciales['password'], $usuario->password)) {
            return back()
                ->withErrors(['email' => 'Las credenciales ingresadas son incorrectas o tu cuenta está deshabilitada.'])
                ->onlyInput('email');
        }

        if ($usuario->rol === RolUsuario::ADMINISTRADOR) {
            $claveBloqueo = $this->claveBloqueoOtp($usuario->id);
            $bloqueadoHasta = Cache::get($claveBloqueo);

            if ($bloqueadoHasta !== null && ! is_numeric($bloqueadoHasta)) {
                Cache::forget($claveBloqueo);
                $bloqueadoHasta = null;
            }

            if (is_numeric($bloqueadoHasta) && now()->timestamp < (int) $bloqueadoHasta) {
                $minutos = max(1, (int) ceil(((int) $bloqueadoHasta - now()->timestamp) / 60));

                return back()
                    ->withErrors(['email' => "Demasiados intentos fallidos de verificación. Intenta nuevamente en {$minutos} minuto(s)."])
                    ->onlyInput('email');
            }

            $usuario->codigosOtp()->delete();

            $codigoPlano = (string) random_int(100000, 999999);
            $expiraEn = now()->addMinutes(5);

            $registroOtp = CodigoOtp::create([
                'user_id' => $usuario->id,
                'codigo' => Hash::make($codigoPlano),
                'intentos' => 0,
                'expira_en' => $expiraEn,
            ]);

            try {
                Mail::to($usuario->email)->send(new CodigoAdminMail($usuario, $codigoPlano));
            } catch (Throwable) {
                $registroOtp->delete();

                return back()
                    ->with('error', 'No se pudo enviar el código de verificación por correo. Intenta nuevamente.')
                    ->onlyInput('email');
            }

            $request->session()->put('pendiente_otp_user_id', $usuario->id);
            $request->session()->put('pendiente_otp_expira_en', $expiraEn->timestamp);

            $rutaFirmada = URL::temporarySignedRoute('admin.otp.vista', $expiraEn);

            return redirect($rutaFirmada)
                ->with('exito', 'Se envió un código de verificación de 6 dígitos a tu correo (vigencia de 5 minutos).');
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->intended(route('panel'));
    }

    public function mostrarOtp(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('pendiente_otp_user_id');
        $expiraSesion = $request->session()->get('pendiente_otp_expira_en');

        if (! $userId || ! $expiraSesion || now()->timestamp > (int) $expiraSesion) {
            $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

            return redirect()->route('login')
                ->with('error', 'Debes completar primero el inicio de sesión inicial.');
        }

        $usuario = User::find($userId);

        if (! $usuario || $usuario->rol !== RolUsuario::ADMINISTRADOR) {
            $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

            return redirect()->route('login')
                ->with('error', 'Sesión de verificación no válida.');
        }

        $otpActivo = $usuario->codigosOtp()->latest()->first();

        if (! $otpActivo || $otpActivo->estaExpirado()) {
            $usuario->codigosOtp()->delete();
            $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

            return redirect()->route('login')
                ->with('error', 'El código de verificación ha expirado (vigencia máxima de 5 minutos). Inicia sesión nuevamente.');
        }

        $actionUrl = URL::temporarySignedRoute('admin.otp.verificar', $otpActivo->expira_en);

        return view('practica1_1.verificacion-otp', [
            'usuario' => $usuario,
            'actionUrl' => $actionUrl,
            'expiraEn' => $otpActivo->expira_en,
        ]);
    }

    public function verificarOtp(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'codigo' => ['required', 'digits:6'],
        ], [
            'codigo.required' => 'Ingresa el código de verificación.',
            'codigo.digits' => 'El código debe contener exactamente 6 dígitos numéricos.',
        ]);

        $userId = $request->session()->get('pendiente_otp_user_id');
        $expiraSesion = $request->session()->get('pendiente_otp_expira_en');

        if (! $userId || ! $expiraSesion || now()->timestamp > (int) $expiraSesion) {
            $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

            return redirect()->route('login')
                ->with('error', 'Tu proceso de verificación expiró o no está activo. Inicia sesión nuevamente.');
        }

        $usuario = User::find($userId);

        if (! $usuario || $usuario->rol !== RolUsuario::ADMINISTRADOR) {
            $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

            return redirect()->route('login')
                ->with('error', 'Usuario no autorizado para verificación.');
        }

        $registroOtp = $usuario->codigosOtp()->latest()->first();

        if (! $registroOtp || $registroOtp->estaExpirado()) {
            $usuario->codigosOtp()->delete();
            $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

            return redirect()->route('login')
                ->with('error', 'El código ha expirado (vigencia máxima de 5 minutos). Inicia sesión de nuevo.');
        }

        if (! Hash::check($datos['codigo'], $registroOtp->codigo)) {
            $nuevosIntentos = $registroOtp->intentos + 1;

            if ($nuevosIntentos >= self::MAX_INTENTOS_OTP) {
                $usuario->codigosOtp()->delete();
                $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);
                Cache::put(
                    $this->claveBloqueoOtp($usuario->id),
                    now()->addMinutes(self::MINUTOS_BLOQUEO_OTP)->timestamp,
                    now()->addMinutes(self::MINUTOS_BLOQUEO_OTP)
                );

                return redirect()->route('login')
                    ->with('error', 'Superaste el límite de intentos incorrectos. Cuenta bloqueada temporalmente por 5 minutos.');
            }

            $registroOtp->update(['intentos' => $nuevosIntentos]);
            $restantes = self::MAX_INTENTOS_OTP - $nuevosIntentos;

            return back()->withErrors([
                'codigo' => "Código incorrecto. Te quedan {$restantes} intento(s).",
            ]);
        }

        $usuario->codigosOtp()->delete();
        Cache::forget($this->claveBloqueoOtp($usuario->id));
        $request->session()->forget(['pendiente_otp_user_id', 'pendiente_otp_expira_en']);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('panel')
            ->with('exito', 'Verificación completada. Bienvenido(a), '.$usuario->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('exito', 'Has cerrado sesión correctamente.');
    }

    private function claveBloqueoOtp(int $userId): string
    {
        return "otp_bloqueo_{$userId}";
    }
}
