<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AyudaController extends Controller
{
    /**
     * Muestra el formulario de ayuda. La ruta está protegida con el middleware "auth",
     * así que solo llega aquí quien ya inició sesión.
     */
    public function create()
    {
        return view('ayuda', ['usuario' => auth()->user()]);
    }

    /**
     * Valida el mensaje y lo envía al correo de Nexora.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'asunto'  => ['required', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'asunto.required'  => __('The subject is required.'),
            'asunto.max'       => __('The subject cannot exceed 150 characters.'),
            'mensaje.required' => __('The message is required.'),
            'mensaje.min'      => __('The message must be at least 10 characters.'),
            'mensaje.max'      => __('The message cannot exceed 2000 characters.'),
        ]);

        $usuario = auth()->user();
        $correoUsuario = $usuario->correo ?? $usuario->email ?? null;
        $nombre = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));

        // Se quitan saltos de línea del asunto para evitar inyección de encabezados.
        $asunto = trim(preg_replace('/\s+/', ' ', $datos['asunto']));

        $cuerpo = "Mensaje de ayuda enviado desde Nexora\n\n"
            . 'De: ' . ($nombre !== '' ? $nombre : 'Usuario') . ' <' . ($correoUsuario ?? 'sin correo') . ">\n\n"
            . $datos['mensaje'];

        try {
            Mail::raw($cuerpo, function ($mensaje) use ($asunto, $correoUsuario, $nombre) {
                // Destino: el correo de Nexora. Cámbialo aquí si quieres otro buzón.
                $mensaje->to(config('mail.from.address'))
                    ->subject('[Ayuda Nexora] ' . $asunto);

                if ($correoUsuario) {
                    $mensaje->replyTo($correoUsuario, $nombre !== '' ? $nombre : null);
                }
            });
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['mensaje' => __('Could not send your message. Please try again later.')]);
        }

        return redirect()
            ->route('ayuda')
            ->with('status', __('Your message was sent. We will reply to your email.'));
    }
}