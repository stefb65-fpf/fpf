<?php

use Illuminate\Support\Facades\Route;


// génération des fichiers excel de routage
Route::post('/editEtiquettes', [App\Http\Controllers\Admin\PublicationController::class, 'createEtiquettes']);
Route::post('/editRoutageFede', [App\Http\Controllers\Admin\PublicationController::class, 'createRoutageFede']);
Route::post('/generateSouscriptionsList', [App\Http\Controllers\Admin\PublicationController::class, 'generateSouscriptionsList']);
Route::post('/generateSouscriptionsColisage', [App\Http\Controllers\Admin\PublicationController::class, 'generateSouscriptionsColisage']);

// mise à jour de l'appartenance au CE d'une fonction FPF
Route::post('/updateFonctionCe', [App\Http\Controllers\Admin\FonctionController::class, 'updateFonctionCe']);
Route::post('/updateAttribution', [App\Http\Controllers\Admin\FonctionController::class, 'updateAttribution']);

// actions sur les utilisateurs
Route::post('/checkRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class, 'checkRenouvellementAdherents']);
Route::post('/validRenouvellementAdherents', [App\Http\Controllers\Api\UtilisateurController::class, 'validRenouvellementAdherents']);
Route::post('/utilisateurs/checkBeforeInsertion', [App\Http\Controllers\Api\UtilisateurController::class, 'checkBeforeInsertion']);
Route::post('/utilisateurs/getTarifForNewUser', [App\Http\Controllers\Api\UtilisateurController::class, 'getTarifForNewUser']);
Route::post('/utilisateurs/register', [App\Http\Controllers\Api\UtilisateurController::class, 'register']);
Route::post('/utilisateurs/renew/individuel', [App\Http\Controllers\Api\UtilisateurController::class, 'renewIndividuel']);
Route::post('/admin/renew/individuel', [App\Http\Controllers\Api\UtilisateurController::class, 'renewIndividuelByAdmin']);
Route::post('/utilisateurs/add/individuel', [App\Http\Controllers\Api\UtilisateurController::class, 'addIndividuel']);
Route::post('/utilisateurs/add/abonnement', [App\Http\Controllers\Api\UtilisateurController::class, 'addAbonnement']);

// action sur les règlements
Route::post('/validReglement', [App\Http\Controllers\Api\ReglementController::class, 'validReglement']);
Route::post('/editCartes', [App\Http\Controllers\Api\ReglementController::class, 'editCartes']);
Route::post('/reEditCarte', [App\Http\Controllers\Api\ReglementController::class, 'reEditCarte'])->name('reEditCarte');
Route::post('/relanceReglement', [App\Http\Controllers\Api\ReglementController::class, 'relanceReglement']);

// gestion de commandes Florilège
Route::post('/florilege/order', [App\Http\Controllers\Api\FlorilegeController::class, 'orderFlorilege']);
Route::post('/florilege/orderClub', [App\Http\Controllers\Api\FlorilegeController::class, 'orderFlorilegeClub']);

// gestion des paiement clubs, modification des données clubs
Route::post('/clubs/payByVirement', [App\Http\Controllers\Api\ClubController::class, 'payByVirement']);
Route::post('/clubs/payByCb', [App\Http\Controllers\Api\ClubController::class, 'payByCb']);
Route::post('/submitClubActivites', [App\Http\Controllers\Api\ClubController::class, 'clubActivite']);
Route::post('/submitClubEquipements', [App\Http\Controllers\Api\ClubController::class, 'clubEquipement']);

// connexion des personnes, gestion de session
Route::post('/personnes/updateSession', [App\Http\Controllers\Api\PersonneController::class, 'updateSession']);
Route::get('/personnes/getSession', [App\Http\Controllers\Api\PersonneController::class, 'getSession']);
Route::get('/personnes/setCookiesForNewsletter', [App\Http\Controllers\Api\PersonneController::class, 'setCookiesForNewsletter']);
Route::get('/isAdmin', [App\Http\Controllers\Api\PersonneController::class, 'isAdmin']);
Route::post('/checkConnexion', [App\Http\Controllers\Api\PersonneController::class, 'checkExternLogin']);
Route::post('/checkConnexionWithoutPassword', [App\Http\Controllers\Api\PersonneController::class, 'checkExternLoginWithoutPaswword']);
Route::post('/getUserForAutoload', [App\Http\Controllers\Api\PersonneController::class, 'getUserForAutoload']);
Route::get('/getStatus/{term}', [App\Http\Controllers\Api\PersonneController::class, 'getStatus']);
Route::post('/personnes/affectationUr', [App\Http\Controllers\Api\PersonneController::class, 'affectationUr']);
Route::post('/submitNewsPreferences', [App\Http\Controllers\Api\PersonneController::class, 'newsPreferences']);

Route::post('/votes/sendCode', [App\Http\Controllers\Api\VoteController::class, 'sendCode']);
Route::post('/votes/confirmCancelCode', [App\Http\Controllers\Api\VoteController::class, 'confirmCancelCode']);
Route::post('/votes/confirmGiveCode', [App\Http\Controllers\Api\VoteController::class, 'confirmGiveCode']);
Route::post('/votes/saveVote', [App\Http\Controllers\Api\VoteController::class, 'saveVote']);

Route::post('/reversements/confirm', [App\Http\Controllers\Api\ReversementController::class, 'validReversement']);

// autocomplétion pour la saisie de commune set code postaux
Route::post('/getAutocompleteCommune', [App\Http\Controllers\Api\CommuneController::class, 'autocompleteCommune']);


// mise à jour des paramètres de configuration saison et tarifs
Route::post('/updateTarif', [App\Http\Controllers\Admin\ConfigController::class, 'updateTarif']);
Route::post('/updateConfig', [App\Http\Controllers\Admin\ConfigController::class, 'updateConfig']);

// mise à jour du sttaut de la demande de support
Route::post('/updateStatusSupport', [App\Http\Controllers\Admin\SupportController::class, 'updateStatus']);
Route::post('/sendAnswerSupport', [App\Http\Controllers\Admin\SupportController::class, 'sendAnswer']);


//liste adhérents club
Route::post('/ajax/editListAdherents', [App\Http\Controllers\Api\UtilisateurController::class, 'createListAdherents']);


Route::post('/gestStatsClub', [App\Http\Controllers\Api\StatistiquesController::class, 'gestStatsClub']);
Route::post('/gestStatsAdherents', [App\Http\Controllers\Api\StatistiquesController::class, 'gestStatsAdherents']);
Route::post('/gestStatsRepartitionCartes', [App\Http\Controllers\Api\StatistiquesController::class, 'gestStatsRepartitionCartes']);


// action sur l'affichage des formations
Route::post('/getFormateur', [App\Http\Controllers\Api\FormationController::class, 'getFormateur']);
Route::post('/formations/setInterest', [App\Http\Controllers\Api\FormationController::class, 'setInterest']);
Route::post('/formations/askFormation', [App\Http\Controllers\Api\FormationController::class, 'askFormation']);
Route::post('/getReviews', [App\Http\Controllers\Api\FormationController::class, 'getReviews']);
Route::post('/formations/payByVirement', [App\Http\Controllers\Api\FormationController::class, 'payByVirement']);
Route::post('/formations/payByCb', [App\Http\Controllers\Api\FormationController::class, 'payByCb']);
Route::post('/formations/inscriptionAttente', [App\Http\Controllers\Api\FormationController::class, 'inscriptionAttente']);
Route::post('/formations/saveWithoutPaiement', [App\Http\Controllers\Api\FormationController::class, 'saveWithoutPaiement']);
Route::post('/formations/addInscritToSession', [App\Http\Controllers\Api\FormationController::class, 'addInscritToSession']);
Route::post('/formations/generatePdfEvaluations', [App\Http\Controllers\Api\FormationController::class, 'generatePdfEvaluations']);
Route::post('/formations/cancelInscription', [App\Http\Controllers\Api\FormationController::class, 'cancelInscription']);

Route::post('/sessions/payByVirement', [App\Http\Controllers\Api\SessionController::class, 'payByVirement']);
Route::post('/sessions/payByCb', [App\Http\Controllers\Api\SessionController::class, 'payByCb']);

Route::post('/checkTrainerEmail', [App\Http\Controllers\Api\FormateurController::class, 'checkTrainerEmail']);

Route::post('/formateurs/upload/{formateur}', [App\Http\Controllers\Api\FormateurController::class, 'upload'])->name('formateurs.upload');
