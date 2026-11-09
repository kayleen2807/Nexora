<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\UserInvitationController;
use App\Http\Controllers\Auth\GoogleController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\SucursalController;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\ProductoController as AdminProductoController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Admin\ResumeController;
use App\Http\Controllers\Gerente\MiSucursalController;
use App\Http\Controllers\Gerente\ProductoController;
use App\Http\Controllers\Gerente\InventarioController as GerenteInventarioController;
use App\Http\Controllers\Gerente\CajaController;
use App\Http\Controllers\Gerente\CompraController;
use App\Http\Controllers\Gerente\HistorialController;
use App\Http\Controllers\Gerente\DevolucionController;
use App\Http\Controllers\Gerente\CorteController as GerenteCorteController;
use App\Http\Controllers\Admin\CorteController as AdminCorteController;
use App\Http\Controllers\Admin\NegocioController;
use App\Http\Controllers\Cajero\TurnoController;
use App\Http\Controllers\Cajero\VentaController as CajeroVentaController;
use App\Http\Controllers\Admin\ReporteController as AdminReporteController;
use App\Http\Controllers\Gerente\ReporteController as GerenteReporteController;
use App\Http\Controllers\Cajero\ReporteController as CajeroReporteController;

//Ruta para la funcion del idioma(local)
Route::get('/lang/{locale}', function ($locale){
    
    if (in_array($locale, ['es', 'en'])){
        Session::put('locale', $locale);
        Cookie::queue('idioma', $locale, 525600);

        /** @var User|null $usuario */
        $usuario = Auth::user();

        if($usuario){
            $usuario->update(['idioma' => $locale]);
        }
    }

    return redirect()->back();
})->name('lang.switch'); 

//Ruta para terminos y privacidad
Route::view('/terminos', 'legal.terminos')->name('legal.terminos');
Route::view('/privacidad', 'legal.privacidad')->name('legal.privacidad');

//Ruta para el login y para cerrar sesion
Route::get('/', fn () => redirect()->route('login'));

// Sirve los archivos públicos también en el servidor local de Windows, donde
// el servidor PHP puede no resolver correctamente el junction public/storage.
Route::get('/product-images/{path}', function (string $path) {
    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})->where('path', '.*');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Registro
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/verify-email', [EmailVerificationController::class, 'show'])->name('verification.notice');
Route::post('/verify-email', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/verify-email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

Route::get('/invitations/{invitation}/{token}', [UserInvitationController::class, 'show'])->name('invitation.accept');
Route::post('/invitations/{invitation}/{token}', [UserInvitationController::class, 'accept']);

Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
//Login y registro con Google
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

//Rutas para cada panel dependiendo el rol
Route::middleware('auth')->group(function (){
    //Redirige a cada rol a su panel
    Route::get('/dashboard', function () {
        /** @var User $usuario */
        $usuario = Auth::user();

        return match (strtolower($usuario->rol->nombre)) {
            'administrador' => redirect()->route('panel.administrador'),
            'gerente' => redirect()->route('panel.gerente'),
            'cajero' => redirect()->route('panel.cajero'),
            default => abort(403),
        };
    })->name('dashboard');

    Route::get('/administrador', fn() => view('auth.administrador'))
        ->middleware('rol:administrador')->name('panel.administrador');

    Route::get('/gerente', fn() => view('auth.gerente'))
        ->middleware('rol:gerente')->name('panel.gerente');
    
    Route::get('/cajero', fn() => view('auth.cajero'))
        ->middleware('rol:cajero')->name('panel.cajero');

});
//Admin
Route::middleware(['auth', 'rol:administrador'])
    ->prefix('administrador')
    ->group(function() {
        Route::get('/sucursales', [SucursalController::class, 'index']);
        Route::post('/sucursales', [SucursalController::class, 'store']);
        Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update']);
        Route::delete('/sucursales/{sucursal}', [SucursalController::class, 'destroy']);

        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::put('/usuarios/{usuario}/rol', [UsuarioController::class, 'actualizarRol']);
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
        Route::post('/usuarios/{usuario}/invitacion', [UsuarioController::class, 'reenviarInvitacion']);

        Route::get('/inventario', [InventarioController::class, 'index']);

        Route::get('/productos', [AdminProductoController::class, 'index']);
        Route::post('/productos/{producto}/foto', [AdminProductoController::class, 'uploadFoto']);

        Route::get('/ventas', [VentaController::class, 'index']);

        Route::get('/resumen', [ResumeController::class, 'index']);

        Route::get('/cortes', [AdminCorteController::class, 'index']);

        Route::get('/negocio', [NegocioController::class, 'show']);
        Route::put('/negocio', [NegocioController::class, 'update']);

        Route::get('/reportes', [AdminReporteController::class, 'index']);
        Route::put('/reportes/{reporte}', [AdminReporteController::class, 'update']);

    });

//Gerente
Route::middleware(['auth', 'rol:gerente'])
    ->prefix('gerente')
    ->group(function () {
        Route::get('/mi-sucursal', [MiSucursalController::class, 'index']);

        Route::get('/productos', [ProductoController::class, 'index']);
        Route::post('/productos', [ProductoController::class, 'store']);
        Route::put('/productos/{producto}', [ProductoController::class, 'update']);
        Route::put('/productos/{producto}/toggle', [ProductoController::class, 'toggleActivo']);
        Route::post('/productos/{producto}/foto', [ProductoController::class, 'uploadFoto']);

        Route::put('/inventario/{producto}', [GerenteInventarioController::class, 'ajustar']);

        Route::get('/cajas', [CajaController::class, 'index']);
        Route::post('/cajas', [CajaController::class, 'store']);
        Route::put('/cajas/{caja}/toggle', [CajaController::class, 'toggle']);
        Route::delete('/cajas/{caja}', [CajaController::class, 'destroy']);

        Route::get('/compras', [CompraController::class, 'index']);
        Route::post('/compras', [CompraController::class, 'store']);

        Route::get('/historial', [HistorialController::class, 'index']);
        Route::get('/ventas/{venta}', [DevolucionController::class, 'show']);
        Route::post('/ventas/{venta}/devolucion', [DevolucionController::class, 'devolver']);
        Route::post('/ventas/{venta}/cancelar', [DevolucionController::class, 'cancelar']);

        Route::get('/cortes', [GerenteCorteController::class, 'index']);

        Route::get('/reportes', [GerenteReporteController::class, 'index']);
        Route::post('/reportes', [GerenteReporteController::class, 'store']);
    });

//Cajero
Route::middleware(['auth', 'rol:cajero'])
    ->prefix('cajero')
    ->group(function () {
        Route::get('/estado', [TurnoController::class, 'estado']);
        Route::post('/turno/abrir', [TurnoController::class, 'abrir']);
        Route::get('/turno/resumen', [TurnoController::class, 'resumen']);
        Route::post('/turno/cerrar', [TurnoController::class, 'cerrar']);

        Route::get('/productos', [CajeroVentaController::class, 'productos']);
        Route::post('/ventas', [CajeroVentaController::class, 'store']);

        Route::get('/reportes', [CajeroReporteController::class, 'index']);
        Route::post('/reportes', [CajeroReporteController::class, 'store']);
    });
