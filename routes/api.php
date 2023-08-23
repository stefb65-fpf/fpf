<?php
use Illuminate\Support\Facades\Route;


// génératin des fichiers excel de routage
Route::post('/editEtiquettes', [App\Http\Controllers\Admin\PublicationController::class,'createEtiquettes']);
Route::post('/editRoutageFede', [App\Http\Controllers\Admin\PublicationController::class,'createRoutageFede']);
Route::post('/generateSouscriptionsList', [App\Http\Controllers\Admin\PublicationController::class,'generateSouscriptionsList']);

// mise à jour de l'appartenance au CE d'une fonction FPF
Route::post('/updateFonctionCe', [App\Http\Controllers\Admin\FonctionController::class,'updateFonctionCe']);

// actions sur les utilisateurs
Route::post('/checkRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'checkRenouvellementAdherents']);
Route::post('/validRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'validRenouvellementAdherents']);
Route::post('/utilisateurs/checkBeforeInsertion', [App\Http\Controllers\Api\UtilisateurController::class,'checkBeforeInsertion']);
Route::post('/utilisateurs/getTarifForNewUser', [App\Http\Controllers\Api\UtilisateurController::class,'getTarifForNewUser']);
Route::post('/utilisateurs/register', [App\Http\Controllers\Api\UtilisateurController::class,'register']);
Route::post('/utilisateurs/renew/individuel', [App\Http\Controllers\Api\UtilisateurController::class,'renewIndividuel']);

// action sur les règlements
Route::post('/validReglement', [App\Http\Controllers\Api\ReglementController::class,'validReglement']);
Route::post('/editCartes', [App\Http\Controllers\Api\ReglementController::class,'editCartes']);

// gestion de commandes Florilège
Route::post('/florilege/order', [App\Http\Controllers\Api\FlorilegeController::class,'orderFlorilege']);
Route::post('/florilege/orderClub', [App\Http\Controllers\Api\FlorilegeController::class,'orderFlorilegeClub']);

// gestion des paiement clubs, modification des données clubs
Route::post('/clubs/payByVirement', [App\Http\Controllers\Api\ClubController::class,'payByVirement']);
Route::post('/clubs/payByCb', [App\Http\Controllers\Api\ClubController::class,'payByCb']);
Route::post('/submitClubActivites', [App\Http\Controllers\Api\ClubController::class,'clubActivite']);
Route::post('/submitClubEquipements', [App\Http\Controllers\Api\ClubController::class,'clubEquipement']);

// connexion des personnes, gestion de session
Route::post('/personnes/updateSession', [App\Http\Controllers\Api\PersonneController::class,'updateSession']);
Route::get('/personnes/getSession', [App\Http\Controllers\Api\PersonneController::class,'getSession']);
Route::get('/personnes/setCookiesForNewsletter', [App\Http\Controllers\Api\PersonneController::class,'setCookiesForNewsletter']);
Route::get('/isAdmin', [App\Http\Controllers\Api\PersonneController::class,'isAdmin']);
Route::post('/checkConnexion', [App\Http\Controllers\Api\PersonneController::class,'checkExternLogin']);
Route::post('/checkConnexionWithoutPassword', [App\Http\Controllers\Api\PersonneController::class,'checkExternLoginWithoutPaswword']);
Route::post('/getUserForAutoload', [App\Http\Controllers\Api\PersonneController::class,'getUserForAutoload']);
Route::get('/getStatus/{term}', [App\Http\Controllers\Api\PersonneController::class,'getStatus']);
Route::post('/personnes/affectationUr', [App\Http\Controllers\Api\PersonneController::class,'affectationUr']);
Route::post('/submitNewsPreferences', [App\Http\Controllers\Api\PersonneController::class,'newsPreferences']);

// autocomplétion pour la saisie de commune set code postaux
Route::post('/getAutocompleteCommune', [App\Http\Controllers\Api\CommuneController::class,'autocompleteCommune']);


// mise à jour des paramètres de configuration saison et tarifs
Route::post('/updateTarif', [App\Http\Controllers\Admin\ConfigController::class,'updateTarif']);
Route::post('/updateConfig', [App\Http\Controllers\Admin\ConfigController::class,'updateConfig']);


//liste adhérents club
Route::post('/ajax/editListAdherents', [App\Http\Controllers\Api\UtilisateurController::class,'createListAdherents']);
