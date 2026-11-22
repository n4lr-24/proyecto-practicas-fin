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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AuthController extends Controller
{
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
            $usuario->codigosOtp()->delete();

            $codigo = (string) random_int(100000, 999999);
            $expiraEn = now()->addMinutes(5);

            CodigoOtp::create([
                'user_id' => $usuario->id,
                'codigo' => $codigo,
                'expira_en' => $expiraEn,
            ]);

            Mail::to($usuario->email)->send(new CodigoAdminMail($usuario, $codigo));

            $request->session()->put('pendiente_otp_user_id', $usuario->id);

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

        if (! $userId) {
            return redirect()->route('login')
                ->with('error', 'Debes completar primero el inicio de sesión inicial.');
        }

        $usuario = User::find($userId);

        if (! $usuario || $usuario->rol !== RolUsuario::ADMINISTRADOR) {
            $request->session()->forget('pendiente_otp_user_id');

            return redirect()->route('login')
                ->with('error', 'Sesión de verificación no válida.');
        }

        $otpActivo = $usuario->codigosOtp()->latest()->first();

        if (! $otpActivo || $otpActivo->estaExpirado()) {
            $usuario->codigosOtp()->delete();
            $request->session()->forget('pendiente_otp_user_id');

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

        if (! $userId) {
            return redirect()->route('login')
                ->with('error', 'Tu proceso de verificación no está activo. Inicia sesión nuevamente.');
        }

        $usuario = User::find($userId);

        if (! $usuario || $usuario->rol !== RolUsuario::ADMINISTRADOR) {
            $request->session()->forget('pendiente_otp_user_id');

            return redirect()->route('login')
                ->with('error', 'Usuario no autorizado para verificación OTP.');
        }

        $registroOtp = $usuario->codigosOtp()
            ->where('codigo', $datos['codigo'])
            ->latest()
            ->first();

        if (! $registroOtp) {
            return back()->withErrors([
                'codigo' => 'El código de verificación es incorrecto.',
            ]);
        }

        if ($registroOtp->estaExpirado()) {
            $usuario->codigosOtp()->delete();
            $request->session()->forget('pendiente_otp_user_id');

            return redirect()->route('login')
                ->with('error', 'El código ha expirado (vigencia máxima de 5 minutos). Inicia sesión de nuevo.');
        }

        $usuario->codigosOtp()->delete();
        $request->session()->forget('pendiente_otp_user_id');

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
}
