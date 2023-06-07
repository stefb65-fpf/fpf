<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages/welcome');
});
Route::get('/mon-profil', function () {
    return view('pages/mon_profil');
});
Route::get('/formations', function () {
    return view('pages/formations');
});
Route::get('/gestion-club', function () {
    return view('pages/gestion_club');
});
Route::get('/gestion-ur', function () {
    return view('pages/gestion_ur');
});
Route::get('/gestion-fpf', function () {
    return view('pages/gestion_fpf');
});

Route::get('/login', function () {
    return view('auth/login');
});
Route::get('/register', function () {
    return view('auth/register');
});
Route::get('/forgotPassword', function () {
    return view('auth/forgotPassword');
});
Route::get('/reinitPassword', function () {
    return view('auth/reinitPassword');
});
Route::get('/registerAbonnement', function () {
    return view('auth/registerAbonnement');
});
Route::get('/registerAdhesion', function () {
    return view('auth/registerAdhesion');
});
Route::get('/registerFormation', function () {
    return view('auth/registerFormation');
});

