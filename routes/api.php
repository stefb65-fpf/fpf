<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/ajax/editEtiquettes', [App\Http\Controllers\Admin\PublicationController::class,'createEtiquettes']);
Route::post('/ajax/editRoutageFede', [App\Http\Controllers\Admin\PublicationController::class,'createRoutageFede']);

Route::post('/updateFonctionCe', [App\Http\Controllers\Admin\FonctionController::class,'updateFonctionCe']);
Route::post('/checkRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'checkRenouvellementAdherents']);
Route::post('/validRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'validRenouvellementAdherents']);
Route::post('/validReglement', [App\Http\Controllers\Api\ReglementController::class,'validReglement']);
Route::post('/editCartes', [App\Http\Controllers\Api\ReglementController::class,'editCartes']);
Route::post('/utilisateurs/checkBeforeInsertion', [App\Http\Controllers\Api\UtilisateurController::class,'checkBeforeInsertion']);
Route::post('/utilisateurs/getTarifForNewUser', [App\Http\Controllers\Api\UtilisateurController::class,'getTarifForNewUser']);
Route::post('/utilisateurs/register', [App\Http\Controllers\Api\UtilisateurController::class,'register']);
Route::post('/utilisateurs/renew/individuel', [App\Http\Controllers\Api\UtilisateurController::class,'renewIndividuel']);

Route::post('/florilege/order', [App\Http\Controllers\Api\FlorilegeController::class,'orderFlorilege']);
Route::post('/florilege/orderClub', [App\Http\Controllers\Api\FlorilegeController::class,'orderFlorilegeClub']);

Route::post('/clubs/payByVirement', [App\Http\Controllers\Api\ClubController::class,'payByVirement']);
Route::post('/clubs/payByCb', [App\Http\Controllers\Api\ClubController::class,'payByCb']);

Route::post('/personnes/updateSession', [App\Http\Controllers\Api\PersonneController::class,'updateSession']);
Route::get('/personnes/getSession', [App\Http\Controllers\Api\PersonneController::class,'getSession']);
Route::get('/personnes/setCookiesForNewsletter', [App\Http\Controllers\Api\PersonneController::class,'setCookiesForNewsletter']);

Route::post('/generateSouscriptionsList', [App\Http\Controllers\Admin\PublicationController::class,'generateSouscriptionsList']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/ajax/getAutocompleteCommune', [App\Http\Controllers\Api\CommuneController::class,'autocompleteCommune']);

// profile personne
Route::post('/ajax/submitNewsPreferences', [App\Http\Controllers\Api\ApiFormValidationController::class,'newsPreferences']);

// gestion club
Route::post('/ajax/submitClubActivites', [App\Http\Controllers\Api\ApiFormValidationController::class,'clubActivite']);
Route::post('/ajax/submitClubEquipements', [App\Http\Controllers\Api\ApiFormValidationController::class,'clubEquipement']);


Route::post('/ajax/updateTarif', [App\Http\Controllers\Admin\ConfigController::class,'updateTarif']);
Route::post('/ajax/updateConfig', [App\Http\Controllers\Admin\ConfigController::class,'updateConfig']);


//liste adhérents club
Route::post('/ajax/editListAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'createListAdherents']);

//topBar search
Route::get('/ajax/isAdmin', [App\Http\Controllers\Api\TopBarController::class,'isAdmin']);


Route::post('/checkConnexion', [App\Http\Controllers\Api\PersonneController::class,'checkExternLogin']);
Route::post('/checkConnexionWithoutPassword', [App\Http\Controllers\Api\PersonneController::class,'checkExternLoginWithoutPaswword']);
Route::post('/getUserForAutoload', [App\Http\Controllers\Api\PersonneController::class,'getUserForAutoload']);
Route::get('/getStatus/{term}', [App\Http\Controllers\Api\PersonneController::class,'getStatus']);
Route::post('/personnes/affectationUr', [App\Http\Controllers\Api\PersonneController::class,'affectationUr']);
