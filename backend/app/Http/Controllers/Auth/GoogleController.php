<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Throwable;

/**
 * Inicio de sesión / registro con Google (OAuth 2.0, flujo "authorization code").
 * No usa paquetes extra: solo el cliente HTTP que ya trae Laravel.
 */
class GoogleController extends Controller
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL = 'https://openidconnect.googleapis.com/v1/userinfo';

    /**
     * Paso 1: manda al usuario a la pantalla de Google para que elija su cuenta.
     */
    public function redirect(Request $request): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors([
                'correo' => __('Google sign-in is not configured yet.'),
            ]);
        }

        // "state" evita ataques CSRF: Google nos lo devuelve y debe coincidir con el guardado en sesión
        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect()->away(self::AUTH_URL . '?' . $query);
    }

    /**
     * Paso 2: Google regresa aquí con un "code". Lo cambiamos por los datos del usuario,
     * buscamos (o creamos) su cuenta en la tabla "usuario" y abrimos sesión.
     */
    public function callback(Request $request): RedirectResponse
    {
        // El usuario canceló o Google devolvió un error
        if ($request->filled('error')) {
            return $this->fail();
        }

        $expectedState = $request->session()->pull('google_oauth_state');

        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            return $this->fail();
        }

        if (! $request->filled('code')) {
            return $this->fail();
        }

        try {
            $tokenRequest = Http::asForm();

            if (! config('services.google.verify_ssl')) {
                $tokenRequest->withoutVerifying();
            }

            $tokenResponse = $tokenRequest->post(self::TOKEN_URL, [
                'code' => $request->query('code'),
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]);

            if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
                return $this->fail();
            }

            $infoRequest = Http::withToken($tokenResponse->json('access_token'));

            if (! config('services.google.verify_ssl')) {
                $infoRequest->withoutVerifying();
            }

            $infoResponse = $infoRequest->get(self::USERINFO_URL);

            if ($infoResponse->failed()) {
                return $this->fail();
            }

            $google = $infoResponse->json();
        } catch (Throwable $e) {
            report($e);

            return $this->fail();
        }

        $correo = strtolower(trim((string) ($google['email'] ?? '')));

        // Solo aceptamos correos que Google ya verificó
        if ($correo === '' || empty($google['email_verified'])) {
            return $this->fail(__('Your Google email is not verified.'));
        }

        $usuario = User::where('correo', $correo)->first();

        if (! $usuario) {
            // Igual que el registro normal: toda cuenta pública nueva es "administrador"
            $usuario = DB::transaction(function () use ($google, $correo, $request) {
                $rol = Rol::firstOrCreate(['nombre' => 'administrador']);

                return User::create([
                    'nombre' => $google['given_name'] ?? $google['name'] ?? $correo,
                    'apellido' => $google['family_name'] ?? '',
                    'correo' => $correo,
                    // La columna es NOT NULL; se guarda una contraseña aleatoria (el cast 'hashed' la encripta)
                    'contrasena' => Str::random(40),
                    'id_rol' => $rol->id_rol,
                    'id_sucursal' => null,
                    'idioma' => $request->cookie('idioma') ?? config('app.locale'),
                ]);
            });
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        // Mismo manejo de idioma que LoginController
        $idiomaActual = $request->cookie('idioma') ?? $usuario->idioma ?? config('app.locale');
        Session::put('locale', $idiomaActual);
        Cookie::queue('idioma', $idiomaActual, 525600);

        return redirect()->intended(route('dashboard'));
    }

    private function fail(?string $mensaje = null): RedirectResponse
    {
        return redirect()->route('login')->withErrors([
            'correo' => $mensaje ?? __('Could not sign in with Google. Please try again.'),
        ]);
    }
}
