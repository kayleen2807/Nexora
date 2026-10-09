<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\View\View;

class RegisterController extends Controller
{
    /**
     * Mostrar el formulario de registro.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Registrar un nuevo usuario. Todo registro público crea una cuenta con rol "administrador".
     */
    public function register(Request $request): RedirectResponse
    {
        $request->merge(['correo' => strtolower(trim((string) $request->input('correo')))]);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'apellido' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateString(),
            ],
            'correo' => ['required', 'string', 'email', 'max:150', 'unique:usuario,correo'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ], [
            'nombre.required' => __('Name is required.'),
            'nombre.max' => __('Name cannot exceed 150 characters.'),
            'apellido.required' => __('First surname is required.'),
            'apellido.max' => __('First surname cannot exceed 150 characters.'),
            'fecha_nacimiento.required' => __('Date of birth is required.'),
            'fecha_nacimiento.date' => __('Date of birth is invalid.'),
            'fecha_nacimiento.before_or_equal' => __('You must be at least 18 years old to register.'),
            'correo.required' => __('Email address is required.'),
            'correo.email' => __('Email address must be valid.'),
            'correo.max' => __('Email address cannot exceed 150 characters.'),
            'correo.unique' => __('This email address is already registered.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'password.confirmed' => __('Passwords do not match.'),
            'password.regex' => __('Password must include an uppercase letter, a number, and a special character.'),
        ]);

        /** Rol y usuario se crean en una sola transacción para evitar un rol huérfano si algo falla */
        try {
            $user = DB::transaction(function () use ($validated) {
                $rol = Rol::firstOrCreate(['nombre' => 'administrador']);

                return User::create([
                    'nombre' => $validated['nombre'],
                    'apellido' => $validated['apellido'],
                    'fecha_nacimiento' => $validated['fecha_nacimiento'],
                    'correo' => $validated['correo'],
                    'contrasena' => $validated['password'],
                    'id_rol' => $rol->id_rol,
                    'id_sucursal' => null,
                ]);
            });
        } catch (QueryException $e) {
            // 23505 = unique_violation en Postgres; cubre la carrera entre la validación y el INSERT
            if ($e->getCode() !== '23505') {
                throw $e;
            }

            throw ValidationException::withMessages([
                'correo' => __('This email address is already registered.'),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
