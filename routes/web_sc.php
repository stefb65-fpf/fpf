<?php
//Route::get('/clubs/gestion_adherents', [App\Http\Controllers\ClubController::class, 'gestionAdherents'])->name('clubs.gestion_adherents');

Route::get('/clubs/gestion_fonctions', [App\Http\Controllers\ClubController::class, 'gestionFonctions'])->name('clubs.gestion_fonctions');
Route::get('/clubs/gestion_reglements', [App\Http\Controllers\ClubController::class, 'gestionReglements'])->name('clubs.gestion_reglements');
Route::get('/clubs/infos_club', [App\Http\Controllers\ClubController::class, 'infosClub'])->name('clubs.infos_club');


Route::get('/urs/infos_ur', [App\Http\Controllers\UrController::class, 'infosUr'])->name('urs.infos_ur');

Route::get('/urs/liste_adherents', [App\Http\Controllers\UrController::class, 'listeAdherents'])->name('urs.liste_adherents');
Route::get('/urs/liste_fonctions', [App\Http\Controllers\UrController::class, 'listeFonctions'])->name('urs.liste_fonctions');
Route::get('/urs/liste_reversements', [App\Http\Controllers\UrController::class, 'listeReversements'])->name('urs.liste_reversements');

Route::resource('/admin/formations', App\Http\Controllers\Admin\FormationController::class);

Route::resource('/admin/votes', App\Http\Controllers\Admin\VoteController::class);
Route::get('/admin/votes/elections/{vote}', [App\Http\Controllers\Admin\VoteController::class, 'electionsList'])->name('votes.elections.index');
Route::get('/admin/votes/elections/{vote}/create', [App\Http\Controllers\Admin\VoteController::class, 'electionsCreate'])->name('votes.elections.create');
Route::post('/admin/votes/elections/{vote}/store', [App\Http\Controllers\Admin\VoteController::class, 'electionsStore'])->name('votes.elections.store');
Route::get('/admin/votes/elections/{vote}/edit/{election}', [App\Http\Controllers\Admin\VoteController::class, 'electionsEdit'])->name('votes.elections.edit');
Route::put('/admin/votes/elections/{vote}/update/{election}', [App\Http\Controllers\Admin\VoteController::class, 'electionsUpdate'])->name('votes.elections.update');
Route::delete('/admin/votes/elections/{vote}/delete/{election}', [App\Http\Controllers\Admin\VoteController::class, 'electionsDestroy'])->name('votes.elections.delete');


Route::get('/admin/votes/{vote}/elections/{election}/candidats', [App\Http\Controllers\Admin\VoteController::class, 'candidatsList'])->name('votes.elections.candidats.index');
Route::post('/admin/votes/{vote}/elections/{election}/candidats', [App\Http\Controllers\Admin\VoteController::class, 'candidatsStore'])->name('votes.elections.candidats.store');
Route::delete('/admin/votes/{vote}/elections/{election}/candidats/{candidat}', [App\Http\Controllers\Admin\VoteController::class, 'candidatsDestroy'])->name('votes.elections.candidats.delete');

Route::get('/admin/personnes/liste_adherents', [App\Http\Controllers\Admin\PersonneController::class, 'listeAdherents'])->name('personnes.liste_adherents');
Route::get('/admin/personnes/liste_abonnes', [App\Http\Controllers\Admin\PersonneController::class, 'listeAbonnes'])->name('personnes.liste_abonnes');
Route::get('/admin/personnes/liste_formateurs', [App\Http\Controllers\Admin\PersonneController::class, 'listeFormateurs'])->name('personnes.liste_formateurs');
Route::resource('/admin/personnes', App\Http\Controllers\Admin\PersonneController::class);
Route::resource('/admin/droits', App\Http\Controllers\Admin\DroitController::class);
Route::delete('/admin/droits/deleteFonction/{droit_id}/{fonction_id}', [App\Http\Controllers\Admin\DroitController::class, 'deleteFonction'])->name('droits.deleteFonction');
Route::delete('/admin/droits/deleteUtilisateur/{droit_id}/{utilisateur_id}', [App\Http\Controllers\Admin\DroitController::class, 'deleteUtilisateur'])->name('droits.deleteUtilisateur');



Route::resource('/admin/urs', App\Http\Controllers\Admin\UrController::class);
Route::get('/admin/fonctions/ca', [App\Http\Controllers\Admin\FonctionController::class, 'ca'])->name('admin.fonctions.ca');
Route::get('/admin/fonctions/ce', [App\Http\Controllers\Admin\FonctionController::class, 'ce'])->name('admin.fonctions.ce');
Route::resource('/admin/fonctions', App\Http\Controllers\Admin\FonctionController::class);

Route::get('/admin/gestion_publications', [App\Http\Controllers\Admin\PublicationController::class, 'index'])->name('admin.gestion_publications');
Route::get('/admin/routage/france_photo', [App\Http\Controllers\Admin\PublicationController::class, 'routageFP'])->name('admin.routage.france_photo');
Route::get('/admin/routage/lettres_fede', [App\Http\Controllers\Admin\PublicationController::class, 'routageFede'])->name('admin.routage.lettres_fede');
Route::get('/admin/etiquettes', [App\Http\Controllers\Admin\PublicationController::class, 'etiquettes'])->name('admin.etiquettes');
Route::get('/admin/emargements', [App\Http\Controllers\Admin\PublicationController::class, 'emargements'])->name('admin.emargements');

Route::get('/admin/generateRoutageFp/{validate}', [App\Http\Controllers\Admin\PublicationController::class, 'generateRoutageFp'])->name('admin.generateRoutageFp');

Route::get('/admin/config', [App\Http\Controllers\Admin\ConfigController::class, 'index'])->name('admin.config');

Route::get('/admin/structures', function () {
    return view('admin/accueil_structure');
})->name('admin.structures');


