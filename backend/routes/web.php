<?php

use App\Http\Controllers\Auth\LoginController;
use App\Models\Rol;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/lang/{locale}', function ($locale){
    
    if (in_array($locale, ['es', 'en'])){
        Session::put('locale', $locale);
    }

    return redirect()->back();
})->name('lang.switch'); 

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware('auth')
    ->name('dashboard');

Route::get('/roles', function () {
    return Rol::all();
});