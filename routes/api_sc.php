<?php
Route::post('/ajax/editEtiquettes', [App\Http\Controllers\Admin\PublicationController::class,'createEtiquettes']);
Route::post('/ajax/editRoutageFede', [App\Http\Controllers\Admin\PublicationController::class,'createRoutageFede']);

Route::post('/ajax/updateFonctionCe', [App\Http\Controllers\Admin\FonctionController::class,'updateFonctionCe']);
