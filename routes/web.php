<?php

use Illuminate\Support\Facades\Route;


Route::get('/registerAdhesion', [App\Http\Controllers\LoginController::class, 'registerAdhesion'])->name('registerAdhesion');

//gestion des adhésions et abonnements club par responsable de clubs
Route::get('/clubs/florilege', [App\Http\Controllers\ClubController::class, 'florilege'])->name('clubs.florilege');
Route::get('/clubs/factures', [App\Http\Controllers\ClubController::class, 'factures'])->name('clubs.factures');
Route::get('/clubs/adherents/{utilisateur_id}/edit', [App\Http\Controllers\ClubController::class, 'editAdherent'])->name('clubs.adherents.edit');
Route::get('/clubs/adherents/create', [App\Http\Controllers\ClubController::class, 'createAdherent'])->name('clubs.adherents.create');
Route::post('/clubs/adherents/store', [App\Http\Controllers\ClubController::class, 'storeAdherent'])->name('clubs.adherents.store');
Route::put('/clubs/adherents/{utilisateur_id}/update', [App\Http\Controllers\ClubController::class, 'updateAdherent'])->name('clubs.adherents.update');
Route::get('/clubs/adherents/{statut?}/{abonnement?}', [App\Http\Controllers\ClubController::class, 'gestionAdherents'])->name('clubs.adherents.index');
Route::get('/clubs/fonctions', [App\Http\Controllers\ClubController::class, 'gestionFonctions'])->name('clubs.fonctions.index');
Route::get('/clubs/reglements', [App\Http\Controllers\ClubController::class, 'gestionReglements'])->name('clubs.reglements.index');
Route::get('/clubs/infos_club', [App\Http\Controllers\ClubController::class, 'infosClub'])->name('clubs.infos_club');
Route::get('/clubs/attente_paiement_validation', [App\Http\Controllers\ClubController::class, 'attentePaiementValidation'])->name('clubs.attente_paiement_validation');
Route::get('/clubs/florilege/attente_paiement_validation', [App\Http\Controllers\ClubController::class, 'attentePaiementValidationFlorilege'])->name('clubs/florilege/attente_paiement_validation');
Route::get('/clubs/validation_paiement_carte', [App\Http\Controllers\ClubController::class, 'validationPaiementCarte'])->name('clubs.validation_paiement_carte');
Route::get('/reglements/notification_paiement', [App\Http\Controllers\ReglementController::class, 'notificationPaiement'])->name('reglements.notification_paiement');
Route::get('/personnes/notification_paiement', [App\Http\Controllers\ReglementController::class, 'notificationPaiementPersonne'])->name('personnes.notification_paiement');
Route::get('/florilege/notification_paiement', [App\Http\Controllers\ReglementController::class, 'notificationPaiementFlorilege'])->name('florilege.notification_paiement');

Route::get('/utilisateurs/attente_paiement_validation', [App\Http\Controllers\UtilisateurController::class, 'attentePaiementValidation'])->name('utilisateurs.attente_paiement_validation');

Route::get('/urs/infos_ur/', [App\Http\Controllers\UrController::class, 'infosUr'])->name('urs.infos_ur');


Route::get('/urs/liste_reversements', [App\Http\Controllers\UrController::class, 'listeReversements'])->name('urs.liste_reversements');
Route::get('/urs/fonctions/liste', [App\Http\Controllers\UrController::class, 'listeFonctions'])->name('urs.fonctions.liste');
Route::get('/urs/{fonction}/change_attribution', [App\Http\Controllers\UrController::class, 'changeAttribution'])->name('urs.fonctions.change_attribution');
Route::delete('/urs/{fonction}/destroy_fonction', [App\Http\Controllers\UrController::class, 'destroyFonction'])->name('urs.fonctions.destroy');
Route::get('/urs/fonctions/create', [App\Http\Controllers\UrController::class, 'createFonction'])->name('urs.fonctions.create');
Route::post('/urs/fonctions/store', [App\Http\Controllers\UrController::class, 'storeFonction'])->name('urs.fonctions.store');
Route::post('/urs/fonctions/{fonction}/update', [App\Http\Controllers\UrController::class, 'updateFonction'])->name('urs.fonctions.update');
Route::put('/urs/infos_ur/', [App\Http\Controllers\UrController::class, 'updateUr'])->name('urs.infos.update');

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

Route::resource('/admin/droits', App\Http\Controllers\Admin\DroitController::class);
Route::delete('/admin/droits/deleteFonction/{droit_id}/{fonction_id}', [App\Http\Controllers\Admin\DroitController::class, 'deleteFonction'])->name('droits.deleteFonction');
Route::delete('/admin/droits/deleteUtilisateur/{droit_id}/{utilisateur_id}', [App\Http\Controllers\Admin\DroitController::class, 'deleteUtilisateur'])->name('droits.deleteUtilisateur');

Route::get('/admin/reglements/cartes', [App\Http\Controllers\Admin\ReglementController::class, 'editionCartes'])->name('reglements.cartes');
//Route::resource('/admin/reglements', App\Http\Controllers\Admin\ReglementController::class);

Route::get('/admin/factures', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.factures');

Route::get('/admin/supports', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('supports.index');

Route::get('/admin/urs/{ur}/fonctions', [App\Http\Controllers\Admin\UrController::class, 'fonctions'])->name('admin.urs.fonctions');
Route::get('/admin/urs/fonctions/{fonction_id}/{ur_id}/change_attribution_fonctionur', [App\Http\Controllers\Admin\UrController::class, 'changeAttributionUr'])->name('admin.urs.fonctions.change_attribution');
Route::delete('/admin/urs/fonctions/{fonction_id}/{ur_id}/destroy_fonctionur', [App\Http\Controllers\Admin\UrController::class, 'destroyFonctionUr'])->name('admin.urs.fonctions.destroy');
Route::post('/admin/urs/fonctions/{fonction_id}/{ur_id}/update', [App\Http\Controllers\Admin\UrController::class, 'updateFonctionForUr'])->name('admin.urs.fonctions.update');
Route::resource('/admin/urs', App\Http\Controllers\Admin\UrController::class);
//Gestion urs par responsable fpf
//Route::get('/admin/urs/{ur}/edit', [App\Http\Controllers\Admin\UrController::class, 'urEdit'])->name('urs.edit');
//Route::put('/urs/update-ur/{ur}', [App\Http\Controllers\Admin\UrController::class, 'updateUr'])->name('updateUr');

Route::get('/admin/fonctions/ca', [App\Http\Controllers\Admin\FonctionController::class, 'ca'])->name('admin.fonctions.ca');
Route::get('/admin/fonctions/ce', [App\Http\Controllers\Admin\FonctionController::class, 'ce'])->name('admin.fonctions.ce');
Route::get('/admin/fonctions/create_ur', [App\Http\Controllers\Admin\FonctionController::class, 'create_ur'])->name('fonctions.create_ur');
Route::post('/admin/fonctions/store_ur', [App\Http\Controllers\Admin\FonctionController::class, 'store_ur'])->name('fonctions.store_ur');
Route::get('/admin/fonctions/{fonction}/edit_ur', [App\Http\Controllers\Admin\FonctionController::class, 'edit_ur'])->name('fonctions.edit_ur');
Route::put('/admin/fonctions/{fonction}/update_ur', [App\Http\Controllers\Admin\FonctionController::class, 'update_ur'])->name('fonctions.update_ur');
Route::delete('/admin/fonctions/{utilisateur}/destroy_ca', [App\Http\Controllers\Admin\FonctionController::class, 'destroy_ca'])->name('fonctions.destroy_ca');
Route::post('/admin/fonctions/add_ca', [App\Http\Controllers\Admin\FonctionController::class, 'add_ca'])->name('fonctions.add_ca');
Route::resource('/admin/fonctions', App\Http\Controllers\Admin\FonctionController::class);

Route::get('/admin/gestion_publications', [App\Http\Controllers\Admin\PublicationController::class, 'index'])->name('admin.gestion_publications');
Route::get('/admin/routage/france_photo', [App\Http\Controllers\Admin\PublicationController::class, 'routageFP'])->name('admin.routage.france_photo');
Route::get('/admin/routage/lettres_fede', [App\Http\Controllers\Admin\PublicationController::class, 'routageFede'])->name('admin.routage.lettres_fede');
Route::get('/admin/etiquettes', [App\Http\Controllers\Admin\PublicationController::class, 'etiquettes'])->name('admin.etiquettes');
Route::get('/admin/florilege', [App\Http\Controllers\Admin\PublicationController::class, 'florilege'])->name('admin.florilege');

Route::get('/admin/generateRoutageFp/{validate}', [App\Http\Controllers\Admin\PublicationController::class, 'generateRoutageFp'])->name('admin.generateRoutageFp');

Route::get('/admin/config', [App\Http\Controllers\Admin\ConfigController::class, 'index'])->name('admin.config');

Route::get('/admin/structures', function () {
    return view('admin/accueil_structure');
})->name('admin.structures');

Route::get('/', [App\Http\Controllers\PersonneController::class, 'accueil'])->name('accueil');
// authentification
Route::get('/login', function () {
    return view('auth/login');
})->name('login');
Route::get('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
Route::post('/autoload', [App\Http\Controllers\LoginController::class, 'autoload'])->name('autoload');
Route::get('/register', function () {
    return view('auth/register');
});
Route::get('/forgotPassword', function () {
    return view('auth/forgotPassword');
})->name('forgotPassword');
Route::post('/setupLogin', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('/autloadFromWp/{securecode}/{id}', [App\Http\Controllers\LoginController::class, 'autloadFromWp']);

Route::get('/changeEmail/{securecode}', [App\Http\Controllers\LoginController::class, 'changeEmail']);
Route::get('/reinitPassword/{securecode}', [App\Http\Controllers\LoginController::class, 'reinitPassword']);
Route::post('/forgotPassword', [App\Http\Controllers\LoginController::class, 'sendResetAccountPasswordLink']);
Route::put('/resetPassword/{personne}', [App\Http\Controllers\LoginController::class, 'resetPassword'])->name('resetPassword');

Route::get('/registerAbonnement', [App\Http\Controllers\LoginController::class, 'registerAbonnement']);
Route::get('/cancel_paiement', [App\Http\Controllers\UtilisateurController::class, 'cancelPaiement']);
Route::get('/validation_paiement_carte', [App\Http\Controllers\UtilisateurController::class, 'validationPaiementCarte']);

Route::get('/cancel_paiement_renew', [App\Http\Controllers\PersonneController::class, 'cancelPaiementRenew']);
Route::get('/personnes/validation_paiement_carte_renew', [App\Http\Controllers\PersonneController::class, 'validationPaiementCarteRenew']);

Route::get('/cancel_paiement_florilege', [App\Http\Controllers\PersonneController::class, 'cancelPaiementFlorilege']);
Route::get('/validation_paiement_carte_florilege', [App\Http\Controllers\PersonneController::class, 'validationPaiementCarteFlorilege']);

Route::get('/cancel_paiement_florilege_club', [App\Http\Controllers\ClubController::class, 'cancelPaiementFlorilege']);
Route::get('/validation_paiement_carte_florilege_club', [App\Http\Controllers\ClubController::class, 'validationPaiementCarteFlorilege']);


// gestion profil de la personne
Route::get('/mes-mails', [App\Http\Controllers\PersonneController::class, 'mesMails']);
Route::get('/mes-actions', [App\Http\Controllers\PersonneController::class, 'mesActions']);
Route::get('/mes-formations', [App\Http\Controllers\PersonneController::class, 'mesFormations']);;
Route::get('/mon-profil', [App\Http\Controllers\PersonneController::class, 'monProfil'])->name('mon-profil');
Route::get('/florilege', [App\Http\Controllers\PersonneController::class, 'florilege'])->name('florilege');
Route::get('/factures', [App\Http\Controllers\PersonneController::class, 'factures'])->name('factures');
Route::put('/update-password/{personne}',[App\Http\Controllers\PersonneController::class, 'updatePassword'])->name('updatePassword');
Route::put('/update-email/{personne}',[App\Http\Controllers\PersonneController::class, 'updateEmail'])->name('updateEmail');
Route::put('/update-civilite/{personne}',[App\Http\Controllers\PersonneController::class, 'updateCivilite'])->name('updateCivilite');
Route::put('/update-adresse/{personne}/{form}',[App\Http\Controllers\PersonneController::class, 'updateAdresse'])->name('updateAdresse');
Route::delete('/anonymize',[App\Http\Controllers\PersonneController::class, 'anonymize'])->name('anonymize');

Route::put('/resetEmail/{personne}', [App\Http\Controllers\LoginController::class, 'resetEmail'])->name('resetEmail');


// affichage des formations et actions liées à l'inscription
Route::get('/formations/accueil', [App\Http\Controllers\FormationController::class, 'accueil'])->name('formations.accueil');

// gestion des clubs par responsable de clubs
Route::get('/clubs/gestion', [App\Http\Controllers\ClubController::class, 'gestion'])->name('clubs.gestion');
Route::put('/update-generalite/{club}', [App\Http\Controllers\ClubController::class, 'updateGeneralite'])->name('clubGestion_updateGeneralite');
Route::put('/update-club-address/{club}', [App\Http\Controllers\ClubController::class, 'updateClubAddress'])->name('clubGestion_updateClubAddress');
Route::put('/update-club-reunion/{club}', [App\Http\Controllers\ClubController::class, 'updateReunion'])->name('clubGestion_updateReunion');

// gestion des fonctions club par responsable de clubs
Route::delete('/delete-club-fonction/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\ClubController::class,'deleteFonction'])->name('deleteFonctionClub');
Route::put('/update-club-fonction/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\ClubController::class,'updateFonction'])->name('updateFonctionClub');
Route::put('/add-club-fonction/{fonction_id}',[App\Http\Controllers\ClubController::class,'addFonction'])->name('addFonctionClub');


// gestion des urs par responsable ur
Route::get('/urs/gestion', [App\Http\Controllers\UrController::class, 'gestion'])->name('urs.gestion');

// route admins
Route::get('/admin', [App\Http\Controllers\Admin\UserController::class, 'accueil'])->name('admin');
Route::get('/admin/informations', [App\Http\Controllers\Admin\UserController::class, 'informations'])->name('admin.informations');



//Gestion clubs par responsable fpf
Route::get('/admin/clubs/adherents/{utilisateur_id}/edit', [App\Http\Controllers\Admin\ClubController::class, 'editAdherent'])->name('admin.clubs.adherents.edit');
Route::put('/admin/adherents/{utilisateur_id}/update', [App\Http\Controllers\Admin\ClubController::class, 'updateAdherent'])->name('admin.adherents.update');
Route::get('/admin/adherents/{club_id}/create', [App\Http\Controllers\Admin\ClubController::class, 'createAdherent'])->name('admin.clubs.adherents.create');
Route::post('/admin/adherents/{club_id}/store', [App\Http\Controllers\Admin\ClubController::class, 'storeAdherent'])->name('admin.clubs.adherents.store');
Route::put('/admin/clubs/{club}/update-generalite', [App\Http\Controllers\Admin\ClubController::class, 'updateGeneralite'])->name('admin.clubs.updateGeneralite');
Route::put('/admin/clubs/{club}/update-club-addresses', [App\Http\Controllers\Admin\ClubController::class, 'updateClubAddress'])->name('admin.clubs.updateClubAddress');
Route::put('/admin/clubs/{club}/update-club-reunion', [App\Http\Controllers\Admin\ClubController::class, 'updateReunion'])->name('admin.clubs.updateReunion');
Route::get('/admin/clubs/{club}/edit', [App\Http\Controllers\Admin\ClubController::class, 'edit'])->name('admin.clubs.edit');
Route::get('/admin/clubs/create', [App\Http\Controllers\Admin\ClubController::class, 'create'])->name('admin.clubs.create');
Route::post('/admin/clubs/store', [App\Http\Controllers\Admin\ClubController::class, 'store'])->name('admin.clubs.store');
Route::get('/admin/clubs/{ur_id?}/{statut?}/{type_carte?}/{abonnement?}/{term?}', [App\Http\Controllers\Admin\ClubController::class, 'index'])->name('admin.clubs.index');
Route::get('/admin/liste_adherent/{club}/{statut?}/{abonnement?}', [App\Http\Controllers\Admin\ClubController::class, 'listeAdherent'])->name('admin.clubs.liste_adherents_club');
Route::get('/admin/liste_fonctions/{club}', [App\Http\Controllers\Admin\ClubController::class, 'listeFonctions'])->name('admin.clubs.liste_fonctions');
Route::put('/admin/update-club-fonction/{club_id}/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\Admin\ClubController::class,'updateFonction'])->name('admin.updateFonctionClub');
Route::put('/admin/add-club-fonction/{club_id}/{fonction_id}',[App\Http\Controllers\Admin\ClubController::class,'addFonction'])->name('admin.addFonctionClub');
Route::delete('/admin/delete-club-fonction/{club_id}/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\Admin\ClubController::class,'deleteFonction'])->name('admin.deleteFonctionClub');
//Route::resource('/admin/clubs', App\Http\Controllers\Admin\ClubController::class);

//gestion clubs par responsable ur
Route::get('/urs/clubs/adherents/{utilisateur_id}/edit', [App\Http\Controllers\UrController::class, 'editAdherent'])->name('urs.clubs.adherents.edit');
Route::get('/urs/clubs/adherents/{club_id}/create', [App\Http\Controllers\UrController::class, 'createAdherent'])->name('urs.clubs.adherents.create');
Route::post('/urs/clubs/adherents/{club_id}/store', [App\Http\Controllers\UrController::class, 'storeAdherent'])->name('urs.adherents.store');
Route::put('/urs/adherents/{utilisateur_id}/update', [App\Http\Controllers\UrController::class, 'updateAdherent'])->name('urs.adherents.update');
Route::get('/urs/liste_clubs/{statut?}/{type_carte?}/{abonnement?}/{term?}', [App\Http\Controllers\UrController::class, 'listeClubs'])->name('urs.liste_clubs');
Route::get('/urs/clubs/liste_adherents/{club}/{statut?}/{abonnement?}',[App\Http\Controllers\UrController::class, 'listeAdherentsClub'])->name('urs.liste_adherents_club');
Route::get('/urs/clubs/{club}', [App\Http\Controllers\UrController::class, 'updateClub'])->name('UrGestion_updateClub');
Route::put('/urs/clubs/update-generalite/{club}', [App\Http\Controllers\UrController::class, 'updateGeneralite'])->name('UrGestion_updateGeneralite');
Route::put('/urs/clubs/update-club-address/{club}', [App\Http\Controllers\UrController::class, 'updateClubAddress'])->name('UrGestion_updateClubAddress');
Route::put('/urs/clubs/update-club-reunion/{club}', [App\Http\Controllers\UrController::class, 'updateReunion'])->name('UrGestion_updateReunion');
Route::get('/urs/personnes/{personne_id}/edit/{view_type}', [App\Http\Controllers\UrController::class,'editPersonne'])->name('urs.personnes.edit');
Route::get('/urs/personnes/create', [App\Http\Controllers\UrController::class,'createPersonne'])->name('urs.personnes.create');
Route::post('/urs/personnes/store', [App\Http\Controllers\UrController::class,'storePersonne'])->name('urs.personnes.store');
Route::put('/urs/personnes/{personne}/update/{view_type}', [App\Http\Controllers\UrController::class,'updatePersonne'])->name('urs.personnes.update');
Route::get('/urs/personnes/{view_type}/{statut?}/{type_carte?}/{type_adherent?}/{term?}', [App\Http\Controllers\UrController::class, 'list']);
Route::get('/urs/liste_fonctions/{club}', [App\Http\Controllers\UrController::class, 'listeFonctionsClub'])->name('urs.clubs.liste_fonctions');
Route::put('/urs/update-club-fonction/{club_id}/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\UrController::class,'updateClubFonction'])->name('urs.updateFonctionClub');
Route::put('/urs/add-club-fonction/{club_id}/{fonction_id}',[App\Http\Controllers\UrController::class,'addClubFonction'])->name('urs.addFonctionClub');
Route::delete('/urs/delete-club-fonction/{club_id}/{current_utilisateur_id}/{fonction_id}',[App\Http\Controllers\UrController::class,'deleteClubFonction'])->name('urs.deleteFonctionClub');

//gestion admin personnes
Route::get('/admin/personnes/{personne_id}/edit/{view_type}', [App\Http\Controllers\Admin\PersonneController::class,'edit'])->name('admin.personnes.edit');
Route::get('/admin/personnes/create/{view_type}', [App\Http\Controllers\Admin\PersonneController::class,'create'])->name('admin.personnes.create');
Route::put('/admin/personnes/{personne}/update/{view_type}', [App\Http\Controllers\Admin\PersonneController::class,'update'])->name('admin.personnes.update');
Route::post('/admin/personnes/store/{view_type}', [App\Http\Controllers\Admin\PersonneController::class,'store'])->name('admin.personnes.store');
Route::delete('/admin/personnes/{personne}/anonymize/{view_type}', [App\Http\Controllers\Admin\PersonneController::class,'anonymize'])->name('admin.personnes.anonymize');
Route::get('/admin/personnes/{view_type}/{ur_id?}/{statut?}/{type_carte?}/{type_adherent?}/{term?}', [App\Http\Controllers\Admin\PersonneController::class, 'list']);
Route::get('/admin/personnes', [App\Http\Controllers\Admin\PersonneController::class,'index'])->name('personnes.index');


//gestion admin reglements
Route::get('/admin/reglements/{term?}', [App\Http\Controllers\Admin\ReglementController::class, 'index']);
Route::resource('/admin/reglements', App\Http\Controllers\Admin\ReglementController::class);

//Gestion support
Route::get('/support', [App\Http\Controllers\SupportController::class,'index'])->name('support.index');
Route::put('/support', [App\Http\Controllers\SupportController::class,'submit'])->name('support.submit');
