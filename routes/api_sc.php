<?php
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

Route::post('/clubs/payByVirement', [App\Http\Controllers\Api\ClubController::class,'payByVirement']);
Route::post('/clubs/payByCb', [App\Http\Controllers\Api\ClubController::class,'payByCb']);
