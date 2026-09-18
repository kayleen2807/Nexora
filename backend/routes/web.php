<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hola', function (){
    return 'Hola desde Nexora';
});

use App\Models\Rol;

Route::get('/roles', function(){
    return Rol::all();
});