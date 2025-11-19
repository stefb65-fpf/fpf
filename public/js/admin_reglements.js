$('a[name=validerReglement]').on('click', function(e){
    e.preventDefault()
    let id = $(this).data('id')
    let reference = $(this).data('reference')
    let montant = $(this).data('montant')
    $('#referenceValidationRenouvellement').html(reference)
    $('#montantValidationRenouvellement').html(montant)
    $('#validRenouvellement').data('id', id)
    console.log(id)
    $('#modalValidationRenouvellement').removeClass('d-none')
})
$('#validRenouvellement').on('click', function(e){
    if ($('#infoValidationRenouvellement').val() == '') {
        alert('Veuillez saisir une information de paiement')
        return
    }
    const id = $(this).data('id')
    $.ajax({
        url:'/api/validReglement',
        type: 'POST',
        data: {
            ref: id,
            infos: $('#infoValidationRenouvellement').val()
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalValidationRenouvellement').addClass('d-none')
            $('#modalReglementOk').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

$('#editerCartes').on('click', function(e){
    e.preventDefault()
    $.ajax({
        url:'/api/editCartes',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            let chaine = ''
            if (reponse.file_cartes != '') {
                chaine += '<li><a target="_blank" href="' + $('#app_url').html() + reponse.file_cartes + '">le fichier des cartes</a></li>'
            }
            if (reponse.file_etiquettes_club != '') {
                chaine += '<li><a target="_blank" href="' + $('#app_url').html() + reponse.file_etiquettes_club + '">le fichier liste pour les clubs</a></li>'
            }
            if (reponse.file_etiquettes_individuels != '') {
                chaine += '<li><a target="_blank" href="' + $('#app_url').html() + reponse.file_etiquettes_individuels + '">le fichier des étiquettes pour les individuels</a></li>'
            }

            $('#listeCartesEditees').html(chaine)
            $('#modalCartesOk').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})
//reload page  without term
$('.searchedTerm .close').on('click', function(){
    $(this).parent().parent().addClass('d-none')
    let url = "/admin/reglements";
    window.location.href = url;
})

$('a[name=relanceMail]').on('click', function(e){
    $('#refRelance').html($(this).data('reference'))
    $('#validRelance').data('id', $(this).data('id'))
    $('#modalRelanceMail').removeClass('d-none')
})
$('#validRelance').on('click', function(e){
    const id = $(this).data('id')
    $.ajax({
        url:'/api/relanceReglement',
        type: 'POST',
        data: {
            ref: id
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalRelanceMail').addClass('d-none')
            $('#modalRelanceOk').removeClass('d-none')
        },
        error: function (e) {
            alert("Une erreur s'est produite")
        }
    });
})

$('a[name=cancelReglement]').on('click', function(e){
    e.preventDefault()
    const id = $(this).data('ref')
    $.ajax({
        url:'/api/checkCancelReglement',
        type: 'POST',
        data: {
            ref: id
        },
        dataType: 'JSON',
        success: function (reponse) {
            console.log(reponse)
            if (reponse.cancel === 1) {
                if (reponse.is_individuel) {
                    // on affiche la modale pour le remboursement de l'individuel
                    $('#nomAdherentRemboursementIndividuel').html(reponse.utilisateurs[0].nom)
                    $('#montantReglementRemboursementIndividuel').html(reponse.reglement.montant)
                    if (reponse.utilisateurs[0].abonnement === 1) {
                        $('#nbNumerosEnvoyesRemboursementIndividuel').html(reponse.utilisateurs[0].nb_numeros_envoyes)
                        $('#abonnementRemboursementIndividuel').removeClass('d-none')
                        $('#noAbonnementRemboursementIndividuel').addClass('d-none')
                    } else {
                        $('#abonnementRemboursementIndividuel').addClass('d-none')
                        $('#noAbonnementRemboursementIndividuel').removeClass('d-none')
                    }
                    if (reponse.utilisateurs[0].photos > 0) {
                        $('#alertPhotosRemboursementIndividuel').removeClass('d-none')
                    } else {
                        $('#alertPhotosRemboursementIndividuel').addClass('d-none')
                    }
                    $('#montantCreanceRemboursementIndividuel').html(reponse.reglement.total_rembourse)
                    $('#validRemboursementIndividuel').data('ref', id)
                    $('#modalRemboursementIndividuel').removeClass('d-none')
                } else {
                    // on affiche la modale pour la sélection des adhérents clubs à rembourser
                    // on parcourt reponse.utilisateurs pour afficher les adhérents avec une case à cocher
                    let chaine = ''
                    reponse.utilisateurs.forEach(function(utilisateur) {
                        chaine += '<div class="form-check mb-3">'
                        chaine += '<input class="form-check-input" type="checkbox" value="' + utilisateur.id + '" id="adhesionClub' + utilisateur.id + '">'
                        chaine += '<label class="form-check-label" style="margin-left: 20px" for="adhesionClub' + utilisateur.id + '">' + utilisateur.identifiant + ' - ' + utilisateur.nom
                        chaine += ' <span  style="font-size: small">(adhésion: ' + utilisateur.tarif + '€'
                        if (utilisateur.abonnement === 1) {
                            chaine += ' - abonnement: ' + utilisateur.tarif_abo + ' € '
                            chaine += ' - numéros envoyés: ' + utilisateur.nb_numeros_envoyes
                            if (utilisateur.nb_numeros_envoyes > 0) {
                                chaine += ' - non remboursé: ' + utilisateur.montant_non_rembourse + '€'
                            }
                        }
                        if (utilisateur.florilege > 0) {
                            chaine += ' - florilege: ' + utilisateur.montant_florilege + ' € '
                        }
                        chaine += ')</span>'
                        chaine += '</label>'
                        chaine += '</div>';
                    })
                    $('#listeAdherentsClubRemboursement').html(chaine)
                    $('#listeAdherentsClubRemboursement').removeClass('d-none')
                    $('#nomClubRemboursement').html(reponse.club.nom)
                    $('#validRemboursementClub').data('ref', id)
                    $('#modalRemboursementClub').removeClass('d-none')
                }
            } else {
                alert("Il n'y a plus d'adhésions actives pour ce règlement, il ne peut pas être annulé")
            }
        },
        error: function (e) {
            alert("Une erreur s'est produite lors de la vérification de l'annulation du règlement")
        }
    });
})

$('#validRemboursementClub').on('click', function(e){
    e.preventDefault()
    const ref = $(this).data('ref')
    const adherents = []
    $('#listeAdherentsClubRemboursement input[type=checkbox]:checked').each(function() {
        adherents.push($(this).val());
    });
    if (adherents.length === 0) {
        alert('Veuillez sélectionner au moins un adhérent à rembourser')
        return
    }

    $.ajax({
        url:'/api/annulationAdhesionClub',
        type: 'POST',
        data: {
            ref: ref,
            adherents: adherents
        },
        dataType: 'JSON',
        success: function (reponse) {
            alert('L\'annulation des adhésions a été effectuée avec succès.');
            window.location.reload();
        },
        error: function (e) {
            let message;
            try {
                const response = JSON.parse(e.responseText);
                if (response.erreur) {
                    message = response.erreur;
                }
            } catch (parseError) {
                console.error('Erreur de parsing JSON :', parseError);
            }
            alert(message);
        }
    })
})

$('#validRemboursementIndividuel').on('click', function(e){
    e.preventDefault()
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/annulationAdhesionIndividuel',
        type: 'POST',
        data: {
            ref: ref
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalRemboursementIndividuel').addClass('d-none')
            alert('L\'annulation de l\'adhésion a été effectuée avec succès.');
            window.location.reload();
        },
        error: function (e) {
            let message;
            // let message = 'Une erreur est survenue lors de la vérification de la fusion.';

            try {
                const response = JSON.parse(e.responseText);
                if (response.erreur) {
                    message = response.erreur;
                }
            } catch (parseError) {
                // Si le JSON est invalide ou autre erreur
                console.error('Erreur de parsing JSON :', parseError);
            }

            alert(message);
        }
    });
})

$('#selectStatutReglement').on('change', function() {
    if ($('#selectStatutReglement option:selected').val() == -1) {
        window.location.href = '/admin/reglements'
    } else {
        if ($('#selectStatutReglement option:selected').val() == 0) {
            window.location.href = '/admin/reglements/st=0'
        } else {
            window.location.href = '/admin/reglements/st=1'
        }
    }
})

$('#inputMontantReglement').on('keypress', function(e) {
    if (e.which === 13) {
        const montant = $('#inputMontantReglement').val()
        if (montant !== '' && parseInt(montant) == montant) {
            window.location.href = '/admin/reglements/mt=' + montant
        }
    }
})

