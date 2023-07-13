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

// gestion profil de la personne
Route::get('/mes-mails', [App\Http\Controllers\PersonneController::class, 'mesMails']);
Route::get('/mes-actions', [App\Http\Controllers\PersonneController::class, 'mesActions']);
Route::get('/mes-formations', [App\Http\Controllers\PersonneController::class, 'mesFormations']);;
Route::get('/mon-profil', [App\Http\Controllers\PersonneController::class, 'monProfil'])->name('mon-profil');
Route::put('/update-password/{personne}',[App\Http\Controllers\PersonneController::class, 'updatePassword'])->name('updatePassword');
Route::put('/update-civilite/{personne}',[App\Http\Controllers\PersonneController::class, 'updateCivilite'])->name('updateCivilite');
Route::put('/update-adresse/{personne}/{form}',[App\Http\Controllers\PersonneController::class, 'updateAdresse'])->name('updateAdresse');

// affichage des formations et actions liées à l'inscription
Route::get('/formations/accueil', [App\Http\Controllers\FormationController::class, 'accueil'])->name('formations.accueil');

// gestion des clubs par responsable de clubs
Route::get('/clubs/gestion', [App\Http\Controllers\ClubController::class, 'gestion'])->name('clubs.gestion');
//Route::get('/clubs/liste_adherents', [App\Http\Controllers\ClubController::class, 'liste_adherents'])->name('clubs.liste_adherents');
Route::put('/update-generalite/{club}', [App\Http\Controllers\ClubController::class, 'updateGeneralite'])->name('clubGestion_updateGeneralite');
Route::put('/update-club-address/{club}', [App\Http\Controllers\ClubController::class, 'updateClubAddress'])->name('clubGestion_updateClubAddress');
Route::put('/update-club-reunion/{club}', [App\Http\Controllers\ClubController::class, 'updateReunion'])->name('clubGestion_updateReunion');

// gestion des fonctions club par responsable de clubs
Route::delete('/delete-club-fonction/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\ClubController::class,'deleteFonction'])->name('deleteFonctionClub');
Route::put('/update-club-fonction/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\ClubController::class,'updateFonction'])->name('updateFonctionClub');
Route::put('/add-club-fonction/{fonction_id}',[App\Http\Controllers\ClubController::class,'addFonction'])->name('addFonctionClub');


// gestion des urs par responsable ur
Route::get('/urs/gestion', [App\Http\Controllers\UrController::class, 'gestion'])->name('urs.gestion');

// route admins
Route::get('/admin', [App\Http\Controllers\Admin\UserController::class, 'accueil'])->name('admin');

//Gestion urs par responsable fpf
Route::get('/admin/urs/{ur}/edit', [App\Http\Controllers\Admin\UrController::class, 'urEdit'])->name('urs.edit');
Route::put('/urs/update-ur/{ur}', [App\Http\Controllers\Admin\UrController::class, 'updateUr'])->name('updateUr');

//Gestion clubs par responsable fpf
Route::put('/admin/update-generalite/{club}', [App\Http\Controllers\Admin\ClubController::class, 'updateGeneralite'])->name('FPFGestion_updateGeneralite');
Route::put('/admin/update-club-address/{club}', [App\Http\Controllers\Admin\ClubController::class, 'updateClubAddress'])->name('FPFGestion_updateClubAddress');
Route::put('/admin/update-club-reunion/{club}', [App\Http\Controllers\Admin\ClubController::class, 'updateReunion'])->name('FPFGestion_updateReunion');

Route::get('/admin/clubs/club/{club}', [App\Http\Controllers\Admin\ClubController::class, 'update'])->name('FPFGestion_updateClub');
Route::get('/admin/clubs/ajouter', [App\Http\Controllers\Admin\ClubController::class, 'create'])->name('admin.clubs.create');
//Route::get('/admin/clubs/store', [App\Http\Controllers\Admin\AdminController::class, 'store'])->name('admin.clubs.store');
Route::get('/admin/clubs/{ur_id?}/{statut?}/{type_carte?}/{abonnement?}', [App\Http\Controllers\Admin\ClubController::class, 'index'])->name('admin.clubs.index');
Route::get('/admin/liste_adherent/{club}', [App\Http\Controllers\Admin\ClubController::class, 'listeAdherent'])->name('admin.clubs.liste_adherents_club');
Route::resource('/admin/clubs', App\Http\Controllers\Admin\ClubController::class);

//gestion clubs par responsable ur
Route::get('/urs/liste_clubs/{statut?}/{type_carte?}/{abonnement?}', [App\Http\Controllers\UrController::class, 'listeClubs'])->name('urs.liste_clubs');
Route::get('/urs/clubs/liste_adherents/{club}',[App\Http\Controllers\UrController::class, 'listeAdherentsClub'])->name('urs.liste_adherents_club');
Route::get('/urs/clubs/{club}', [App\Http\Controllers\UrController::class, 'updateClub'])->name('UrGestion_updateClub');
Route::put('/urs/clubs/update-generalite/{club}', [App\Http\Controllers\UrController::class, 'updateGeneralite'])->name('UrGestion_updateGeneralite');
Route::put('/urs/clubs/update-club-address/{club}', [App\Http\Controllers\UrController::class, 'updateClubAddress'])->name('UrGestion_updateClubAddress');
Route::put('/urs/clubs/update-club-reunion/{club}', [App\Http\Controllers\UrController::class, 'updateReunion'])->name('UrGestion_updateReunion');

