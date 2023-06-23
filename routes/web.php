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
include 'web_sc.php';

Route::get('/login', function () {
    return view('auth/login');
})->name('login');
Route::get('/register', function () {
    return view('auth/register');
});
Route::get('/forgotPassword', function () {
    return view('auth/forgotPassword');
})->name('forgotPassword');

Route::get('/reinitPassword/{securecode}', [App\Http\Controllers\LoginController::class, 'reinitPassword']);
Route::post('/forgotPassword', [App\Http\Controllers\LoginController::class, 'sendResetAccountPasswordLink']);
Route::put('/resetPassword/{personne}', [App\Http\Controllers\LoginController::class, 'resetPassword'])->name('resetPassword');
//Route::get('/registerAbonnement', function () {
//    return view('auth/registerAbonnement');
//});
Route::get('/registerAbonnement', [App\Http\Controllers\LoginController::class, 'registerAbonnement']);


Route::get('/mes-mails', [App\Http\Controllers\PageController::class, 'mesMails']);
Route::get('/mes-actions', function () {
    return view('account/mes_actions');
});
Route::get('/mes-formations', function () {
    return view('account/mes_formations');
});
Route::get('/mon-profil', function () {
    return view('account/mon_profil');
});
Route::get('/registerAdhesion', function () {
    return view('auth/registerAdhesion');
});
Route::get('/registerFormation', function () {
    return view('auth/registerFormation');
});

