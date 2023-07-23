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

include 'api_sc.php';
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

