<?php

namespace App\Http\Controllers\Practica1_2;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::orderBy('id')->get();
        $totalEliminados = User::onlyTrashed()->count();

        return view('practica1_2.index', compact('usuarios', 'totalEliminados'));
    }

    public function create(): View
    {
        $roles = RolUsuario::cases();

        return view('practica1_2.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', Rule::in(RolUsuario::valores())],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'rol.required' => 'Selecciona un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => $datos['rol'],
        ]);

        return redirect()->route('usuarios.index')
            ->with('exito', 'Usuario registrado correctamente.');
    }

    public function show(User $usuario): View
    {
        return view('practica1_2.show', compact('usuario'));
    }

    public function edit(User $usuario): View
    {
        $roles = RolUsuario::cases();

        return view('practica1_2.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', Rule::in(RolUsuario::valores())],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya pertenece a otro usuario.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'rol.required' => 'Selecciona un rol.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        $actualizacion = [
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
        ];

        if (! empty($datos['password'])) {
            $actualizacion['password'] = Hash::make($datos['password']);
        }

        $usuario->update($actualizacion);

        return redirect()->route('usuarios.index')
            ->with('exito', 'Información del usuario actualizada correctamente.');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes deshabilitar tu propia cuenta de administrador.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('exito', "Usuario \"{$usuario->name}\" deshabilitado (enviado a la Papelera).");
    }

    public function papelera(): View
    {
        $usuariosEliminados = User::onlyTrashed()->latest('deleted_at')->get();

        return view('practica1_2.papelera', compact('usuariosEliminados'));
    }

    public function restaurar(int $id): RedirectResponse
    {
        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()->route('usuarios.papelera')
            ->with('exito', "Usuario \"{$usuario->name}\" restaurado correctamente.");
    }

    public function forceDelete(Request $request, int $id): RedirectResponse
    {
        $usuario = User::withTrashed()->findOrFail($id);

        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar permanentemente tu propia cuenta de administrador.');
        }

        $nombre = $usuario->name;
        $estabaEnPapelera = $usuario->trashed();
        $usuario->forceDelete();

        return redirect()
            ->route($estabaEnPapelera ? 'usuarios.papelera' : 'usuarios.index')
            ->with('exito', "Usuario \"{$nombre}\" eliminado permanentemente de la base de datos.");
    }
}
