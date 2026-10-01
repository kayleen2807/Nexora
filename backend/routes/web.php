<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\Rol;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\SucursalController;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Admin\ResumeController;
use App\Http\Controllers\Gerente\MiSucursalController;
use App\Http\Controllers\Gerente\ProductoController;

//Ruta para la funcion del idioma(local)
Route::get('/lang/{locale}', function ($locale){
    
    if (in_array($locale, ['es', 'en'])){
        Session::put('locale', $locale);
        Cookie::queue('idioma', $locale, 525600);

        if(auth()->check()){
            auth()->user()->update(['idioma' => $locale]);
        }
    }

    return redirect()->back();
})->name('lang.switch'); 

//Ruta para el login y para cerrar sesion
Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Registro
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware('auth')
    ->name('dashboard');

//Rutas para cada panel dependiendo el rol
Route::middleware('auth')->group(function (){
    //Redirige a cada rol a su panel
    Route::get('/dashboard', function () {
        return match (strtolower(auth()->user()->rol->nombre)) {
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

        Route::get('/inventario', [InventarioController::class, 'index']);

        Route::get('/ventas', [VentaController::class, 'index']);

        Route::get('/resumen', [ResumeController::class, 'index']);

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
    });
