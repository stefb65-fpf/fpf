<?php
Route::post('/setupLogin', [App\Http\Controllers\LoginController::class, 'login']);
//Route::get('/test/test', [App\Http\Controllers\LoginController::class, 'test']);


Route::get('/', [App\Http\Controllers\PageController::class, 'accueil'])->name('accueil');

Route::get('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('login');
Route::get('/mon-compte', [App\Http\Controllers\PageController::class, 'monCompte']);
Route::get('/formations', [App\Http\Controllers\PageController::class, 'formations']);
Route::get('/gestion-club', [App\Http\Controllers\PageController::class, 'gestionClub']);
Route::get('/gestion-ur', [App\Http\Controllers\PageController::class, 'gestionUr']);
Route::get('/gestion-fpf', [App\Http\Controllers\PageController::class, 'gestionFpf']);
