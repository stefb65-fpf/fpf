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
