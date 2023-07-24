<?php
Route::post('/ajax/editEtiquettes', [App\Http\Controllers\Admin\PublicationController::class,'createEtiquettes']);
Route::post('/ajax/editRoutageFede', [App\Http\Controllers\Admin\PublicationController::class,'createRoutageFede']);

Route::post('/ajax/updateFonctionCe', [App\Http\Controllers\Admin\FonctionController::class,'updateFonctionCe']);
Route::post('/ajax/checkRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'checkRenouvellementAdherents']);
Route::post('/ajax/validRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'validRenouvellementAdherents']);
Route::post('/ajax/validReglement', [App\Http\Controllers\Api\ReglementController::class,'validReglement']);
Route::post('/ajax/editCartes', [App\Http\Controllers\Api\ReglementController::class,'editCartes']);
