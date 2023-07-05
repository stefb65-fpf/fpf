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

Route::get('/', [App\Http\Controllers\PersonneController::class, 'accueil'])->name('accueil');


// authentification
Route::get('/login', function () {
    return view('auth/login');
})->name('login');
Route::get('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
Route::get('/register', function () {
    return view('auth/register');
});
Route::get('/forgotPassword', function () {
    return view('auth/forgotPassword');
})->name('forgotPassword');
Route::post('/setupLogin', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('/reinitPassword/{securecode}', [App\Http\Controllers\LoginController::class, 'reinitPassword']);
Route::post('/forgotPassword', [App\Http\Controllers\LoginController::class, 'sendResetAccountPasswordLink']);
Route::put('/resetPassword/{personne}', [App\Http\Controllers\LoginController::class, 'resetPassword'])->name('resetPassword');
Route::get('/registerAbonnement', [App\Http\Controllers\LoginController::class, 'registerAbonnement']);
//Route::get('/registerAdhesion', function () {
//    return view('auth/registerAdhesion');
//});
//Route::get('/registerFormation', function () {
//    return view('auth/registerFormation');
//});

// gestion profil de la personne
Route::get('/mes-mails', [App\Http\Controllers\PersonneController::class, 'mesMails']);
Route::get('/mes-actions', [App\Http\Controllers\PersonneController::class, 'mesActions']);
Route::get('/mes-formations', [App\Http\Controllers\PersonneController::class, 'mesFormations']);;
Route::get('/mon-profil', [App\Http\Controllers\PersonneController::class, 'monProfil'])->name('mon-profil');;
Route::put('/update-password/{personne}',[App\Http\Controllers\PersonneController::class, 'updatePassword'])->name('updatePassword');
Route::put('/update-civilite/{personne}',[App\Http\Controllers\PersonneController::class, 'updateCivilite'])->name('updateCivilite');
Route::put('/update-adresse/{personne}/{form}',[App\Http\Controllers\PersonneController::class, 'updateAdresse'])->name('updateAdresse');




// affichage des formations et actions liées à l'inscription
Route::get('/formations/accueil', [App\Http\Controllers\FormationController::class, 'accueil'])->name('formations.accueil');

// gestion des clubs par responsable de clubs
Route::get('/clubs/gestion', [App\Http\Controllers\ClubController::class, 'gestion'])->name('clubs.gestion');
//Route::get('/clubs/liste_adherents', [App\Http\Controllers\ClubController::class, 'liste_adherents'])->name('clubs.liste_adherents');


// gestion des urs par responsable ur
Route::get('/urs/gestion', [App\Http\Controllers\UrController::class, 'gestion'])->name('urs.gestion');


// route admins
Route::get('/admin', [App\Http\Controllers\Admin\UserController::class, 'accueil'])->name('admin');




