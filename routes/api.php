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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/ajax/getAutocompleteCommune', [App\Http\Controllers\Api\CommuneController::class,'autocompleteCommune']);
Route::post('/ajax/submitNewsPreferences', [App\Http\Controllers\Api\ApiFormValidationController::class,'newsPreferences']);
