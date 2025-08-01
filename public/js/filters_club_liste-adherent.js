//on filter change get new url according to parameters
let rootUrl = window.location.href
let club = $('.pageTitle').attr('data-club')

const checkRenouvellementActif = () => {
    let actif = 0
    if ($('#abonnementClub').is(':checked')) {
        actif = 1
    }
    $('select[name=adh_abo]').each(function () {
        if ($(this).val() != 0) {
            actif = 1
        }
    })
    $('input[name=florilege]').each(function () {
        if ($(this).val() > 0) {
            actif = 1
        }
    })
    if ($('#florilegeClub').val() > 0) {
        actif = 1
    }
    if (actif == 1) {
        $('#renouvellementAdherents').removeAttr('disabled')
    } else {
        $('#renouvellementAdherents').attr('disabled', 'disabled')
    }
    return actif
}

$('select[name=filter]').on('change', function (e) {
    e.preventDefault()
    let statut = $('select[data-ref=statut]').val()
    if (statut == '') {
        return
    }
    let abonnement = $('select[data-ref=abonnement]').val()
    let url
    if (typeof club === 'undefined') {
        url = rootUrl.split('adherents')[0] + "adherents" + "/" + statut + "/" + abonnement
    } else {
        url = rootUrl.split(club)[0] + club + "/" + statut + "/" + abonnement;
    }
    window.location.href = url;
})


$('select[name=selectCt]').on('change', function (e) {
    if ($(this).val() == 5 || $(this).val() == 6) {
        $(this).parent().find('div[name=divSecondeCarte]').removeClass('d-none')
    } else {
        $(this).parent().find('div[name=divSecondeCarte]').addClass('d-none')
    }
})

$('#abonnementClub').on('click', function (e) {
    checkRenouvellementActif()
    // if ($(this).is(':checked')) {
    //     $('#renouvellementAdherents').removeAttr('disabled')
    // } else {
    //     let selected = false
    //     $('input[name=adherer]').each(function () {
    //         if ($(this).is(':checked')) {
    //             selected = true
    //         }
    //     })
    //     $('input[name=abonner]').each(function () {
    //         if ($(this).is(':checked')) {
    //             selected = true
    //         }
    //     })
    //     if (!selected) {
    //         $('#renouvellementAdherents').attr('disabled', 'disabled')
    //     } else {
    //         $('#renouvellementAdherents').removeAttr('disabled')
    //     }
    // }
})

// $('input[name=adherer]').on('click', function (e) {
//     if ($(this).is(':checked')) {
//         $('#renouvellementAdherents').removeAttr('disabled')
//     } else {
//         let selected = false
//         $('input[name=adherer]').each(function () {
//             console.log($(this).is(':checked'))
//             if ($(this).is(':checked')) {
//                 selected = true
//             }
//         })
//         $('input[name=abonner]').each(function () {
//             if ($(this).is(':checked')) {
//                 selected = true
//             }
//         })
//         if (!selected) {
//             $('#renouvellementAdherents').attr('disabled', 'disabled')
//         } else {
//             $('#renouvellementAdherents').removeAttr('disabled')
//         }
//     }
// })
// $('input[name=abonner]').on('click', function (e) {
//     if ($(this).is(':checked')) {
//         $('#renouvellementAdherents').removeAttr('disabled')
//     } else {
//         let selected = false
//         $('input[name=adherer]').each(function () {
//             if ($(this).is(':checked')) {
//                 selected = true
//             }
//         })
//         $('input[name=abonner]').each(function () {
//             if ($(this).is(':checked')) {
//                 selected = true
//             }
//         })
//         if (!selected) {
//             $('#renouvellementAdherents').attr('disabled', 'disabled')
//         } else {
//             $('#renouvellementAdherents').removeAttr('disabled')
//         }
//     }
// })

$('select[name=adh_abo]').on('change', function (e) {
    checkRenouvellementActif()
})

$('#florilegeClub').on('change', function (e) {
    checkRenouvellementActif()
})
$('#florilegeClub').on('keyup', function (e) {
    checkRenouvellementActif()
})

$('input[name=florilege]').on('change', function (e) {
    checkRenouvellementActif()
})
$('input[name=florilege]').on('keyup', function (e) {
    checkRenouvellementActif()
})

$('#renouvellementAdherents').on('click', function (e) {
    // on récupère tous les id des adhérents sélectionnés
    let idAdherents = []
    let idAbonnes = []
    let idFlorileges = []
    const regIdentifiant = /^[0-9]{2}-[0-9]{4}-[0-9]{4}$/
    let passage = 1

    const statut = $(this).data('statut')

    if (typeof $('#florilegeClub').val() != 'undefined' && $('#florilegeClub').val() != parseInt($('#florilegeClub').val())) {
        alert('Le montant du florilège doit être un nombre entier')
        return
    }

    $('select[name=adh_abo]').each(function () {
        const item = $(this)
        const ref = item.data('ref')
        if (item.val() == 1 || item.val() == 3) {
            if ($('#selectCt_' + ref).val() == 5 || $('#selectCt_' + ref).val() == 6) {
                if ($('#secondeCarte_' + ref).val() == '') {
                    alert('Veuillez renseigner le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant'))
                    passage = 0
                    return
                }
                if (!regIdentifiant.test($('#secondeCarte_' + ref).val())) {
                    alert('le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant') + ' est incorrect')
                    passage = 0
                    return
                }
            }
            const line = {
                id: item.data('ref'),
                ct: $('#selectCt_' + ref).val(),
                secondeCarte: $('#secondeCarte_' + ref).val()
            }
            idAdherents.push(line)
        }
        if (item.val() == 2 || item.val() == 3) {
            idAbonnes.push($(this).data('ref'))
        }
    })

    $('input[name=florilege]').each(function () {
        if ($(this).val() != parseInt($(this).val())) {
            alert('Le nombe de florilège doit être un nombre entier')
            passage = 0
            return
        } else {
            if ($(this).val() > 0) {
                const line = {
                    id: $(this).data('ref'),
                    quantite: $(this).val()
                }
                idFlorileges.push(line)
            }
        }
    })

    // $('input[name=adherer]').each(function () {
    //     const item = $(this)
    //     if (item.is(':checked')) {
    //         // on contrôle que le ct et la seconde carte sont bien renseignés
    //         const ref = item.data('ref')
    //         if ($('#selectCt_' + ref).val() == 5 || $('#selectCt_' + ref).val() == 6) {
    //             if ($('#secondeCarte_' + ref).val() == '') {
    //                 alert('Veuillez renseigner le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant'))
    //                 passage = 0
    //                 return
    //             }
    //             if (!regIdentifiant.test($('#secondeCarte_' + ref).val())) {
    //                 alert('le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant') + ' est incorrect')
    //                 passage = 0
    //                 return
    //             }
    //         }
    //         const line = {
    //             id: item.data('ref'),
    //             ct: $('#selectCt_' + ref).val(),
    //             secondeCarte: $('#secondeCarte_' + ref).val()
    //         }
    //         idAdherents.push(line)
    //     }
    // })

    if (passage == 0) {
        return
    }

    // si le statut du club n'est pas 2 et que la longueur de idadhrents est inférieure à 2, on sort
    if (statut != 2 && idAdherents.length < 1) {
        alert('Vous devez renouveler au moins 1 adhérent')
        return
    }

    // $('input[name=abonner]').each(function () {
    //     if ($(this).is(':checked')) {
    //         idAbonnes.push($(this).data('ref'))
    //     }
    // })
    const aboClub = $('#abonnementClub').is(':checked') ? 1 : 0
    const florilegeClub = typeof $('#florilegeClub').val() != 'undefined' ? $('#florilegeClub').val() : 0

    $('#renouvellementListe').html('')
    $.ajax({
        url: '/api/checkRenouvellementAdherents',
        type: 'POST',
        data: {
            adherents: idAdherents,
            abonnes: idAbonnes,
            florileges: idFlorileges,
            club: $('#renouvellementAdherents').data('club'),
            aboClub: aboClub,
            florilegeClub: florilegeClub
        },
        dataType: 'JSON',
        success: function (reponse) {
            $.each(reponse.adherents, function (index, item) {
                let chaine = '<div class="d-flex w100 justify-around line-bordereau">'
                chaine += '<div class="flex-2 small">' + item.adherent.identifiant + ' ' + item.adherent.nom + ' ' + item.adherent.prenom + '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.adherent.ct !== 'undefined' ? item.adherent.ct : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.adhesion !== 'undefined' ? item.adhesion + '€' : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.abonnement !== 'undefined' ? item.abonnement + '€' : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.florilege !== 'undefined' ? item.florilege + '€' : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">' + item.total + '€</div>'
                chaine += '</div>'
                $('#renouvellementListe').append(chaine)
            })
            if (reponse.montant_abonnement_club != 0 || reponse.montant_adhesion_club != 0 || reponse.montant_florilege_club != 0) {
                if (reponse.montant_abonnement_club != 0) {
                    $('#montantClubAbonnement').html(reponse.montant_abonnement_club)
                    $('#divRenouvellementAbonnementClub').removeClass('d-none')
                }
                if (reponse.montant_florilege_club != 0) {
                    $('#montantClubFlorilege').html(reponse.montant_florilege_club)
                    $('#divRenouvellementFlorilegeClub').removeClass('d-none')
                }
                if (reponse.montant_adhesion_club != 0) {
                    $('#montantClubAdhesion').html(reponse.montant_adhesion_club)
                    $('#montantClubAdhesionUr').html(reponse.montant_adhesion_club_ur)
                    $('#divRenouvellementAdhesionClub').removeClass('d-none')
                }
                $('#divRenouvellementClub').removeClass('d-none')
            }
            $('#montantRenouvellementAdhesion').html(reponse.total_adhesion)
            $('#montantRenouvellementAbonnement').html(reponse.total_abonnement)
            $('#montantRenouvellementFlorilege').html(reponse.total_florilege)
            $('#montantRenouvellement').html(reponse.total_montant)
            if (reponse.montant_creance > 0) {
                $('#montantAvoirClub').html(reponse.montant_creance)
                $('#montantRenouvellementTotal').html(reponse.total_to_paid)
                $('#montantAvecCreance').removeClass('d-none')
                $('#montantSansCreance').addClass('d-none')
            } else {
                $('#montantAvecCreance').addClass('d-none')
                $('#montantSansCreance').removeClass('d-none')
            }
            $('#modalRenouvellement').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

$('#btnRenouvellement').on('click', function (e) {
    // on valide les données saisies
    let idAdherents = []
    let idAbonnes = []
    let idFlorileges = []
    const regIdentifiant = /^[0-9]{2}-[0-9]{4}-[0-9]{4}$/
    let passage = 1

    if (typeof $('#florilegeClub').val() != 'undefined' && $('#florilegeClub').val() != parseInt($('#florilegeClub').val())) {
        alert('Le montant du florilège doit être un nombre entier')
        return
    }

    $('select[name=adh_abo]').each(function () {
        const item = $(this)
        const ref = item.data('ref')
        if (item.val() == 1 || item.val() == 3) {
            if ($('#selectCt_' + ref).val() == 5 || $('#selectCt_' + ref).val() == 6) {
                if ($('#secondeCarte_' + ref).val() == '') {
                    alert('Veuillez renseigner le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant'))
                    passage = 0
                    return
                }
                if (!regIdentifiant.test($('#secondeCarte_' + ref).val())) {
                    alert('le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant') + ' est incorrect')
                    passage = 0
                    return
                }
            }
            const line = {
                id: item.data('ref'),
                ct: $('#selectCt_' + ref).val(),
                secondeCarte: $('#secondeCarte_' + ref).val()
            }
            idAdherents.push(line)
        }
        if (item.val() == 2 || item.val() == 3) {
            idAbonnes.push($(this).data('ref'))
        }
    })

    $('input[name=florilege]').each(function () {
        if ($(this).val() != parseInt($(this).val())) {
            alert('Le nombe de florilège doit être un nombre entier')
            passage = 0
            return
        } else {
            if ($(this).val() > 0) {
                const line = {
                    id: $(this).data('ref'),
                    quantite: $(this).val()
                }
                idFlorileges.push(line)
            }
        }
    })


    // $('input[name=adherer]').each(function () {
    //     const item = $(this)
    //     if (item.is(':checked')) {
    //         // on contrôle que le ct et la seconde carte sont bien renseignés
    //         const ref = item.data('ref')
    //         if ($('#selectCt_' + ref).val() == 5 || $('#selectCt_' + ref).val() == 6) {
    //             if ($('#secondeCarte_' + ref).val() == '') {
    //                 alert('Veuillez renseigner le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant'))
    //                 return
    //             }
    //             if (!regIdentifiant.test($('#secondeCarte_' + ref).val())) {
    //                 alert('le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant') + ' est incorrect')
    //                 return
    //             }
    //         }
    //         const line = {
    //             id: item.data('ref'),
    //             ct: $('#selectCt_' + ref).val(),
    //             secondeCarte: $('#secondeCarte_' + ref).val()
    //         }
    //         idAdherents.push(line)
    //     }
    // })

    // $('input[name=abonner]').each(function () {
    //     if ($(this).is(':checked')) {
    //         idAbonnes.push($(this).data('ref'))
    //     }
    // })
    const aboClub = $('#abonnementClub').is(':checked') ? 1 : 0
    const florilegeClub = typeof $('#florilegeClub').val() != 'undefined' ? $('#florilegeClub').val() : 0
    $.ajax({
        url: '/api/validRenouvellementAdherents',
        type: 'POST',
        data: {
            adherents: idAdherents,
            abonnes: idAbonnes,
            club: $('#renouvellementAdherents').data('club'),
            aboClub: aboClub,
            florileges: idFlorileges,
            florilegeClub: florilegeClub
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalRenouvellement').addClass('d-none')
            $('#lienBordereauClub').attr('href', $('#app_url').html() + reponse.file)
            if (reponse.montant_paye > 0) {
                $('#clubPayVirement').data('ref', reponse.reglement_id).removeClass('d-none')
                $('#clubPayCb').data('ref', reponse.reglement_id).removeClass('d-none')
            } else {
                $('#clubPayVirement').addClass('d-none')
                $('#clubPayCb').addClass('d-none')
            }
            $('#modalRenouvellementOk').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

$('#btnFusionAdherents').on('click', function (e) {
    e.preventDefault()
    $('#btnCancelFusion').html('Annuler')
    $('#fusionDemande').removeClass('d-none')
    $('#btnValiderFusion').addClass('d-none')
    $('#fusionResultat').html('')
    $('#fusionResultat').addClass('d-none')
    $('#idFusionMaitre').val('')
    $('#idFusionEsclave').val('')
    $('#btnCheckFusion').removeClass('d-none')
    $('#modalFusion').removeClass('d-none')
})

$('#btnCheckFusion').on('click', function (e) {
    if ($('#idFusionMaitre').val() == '' || $('#idFusionEsclave').val() == '') {
        alert('Veuillez renseigner les deux identifiants')
        return
    }
    // on controle que les deux identifiant doivent être de type xx-xxxx-xxxx avec x comme chiffre
    const regIdentifiant = /^[0-9]{2}-[0-9]{4}-[0-9]{4}$/
    if (!regIdentifiant.test($('#idFusionMaitre').val()) || !regIdentifiant.test($('#idFusionEsclave').val())) {
        alert('Les identifiants doivent être au format xx-xxxx-xxxx')
        return
    }
    // on contrôle que les deux indeitifiants ne sont pas identiques
    if ($('#idFusionMaitre').val() == $('#idFusionEsclave').val()) {
        alert('Les deux identifiants doivent être différents')
        return
    }

    const control = $(this).data('control')
    // on vérifie que les deux identifiants commencent par la même chaine que control
    if (!$('#idFusionMaitre').val().startsWith(control) || !$('#idFusionEsclave').val().startsWith(control)) {
        alert('Les deux identifiants doivent commencer par ' + control)
        return
    }

    $.ajax({
        url: '/api/checkFusionAdherents',
        type: 'POST',
        data: {
            idMaitre: $('#idFusionMaitre').val(),
            idEsclave: $('#idFusionEsclave').val()
        },
        dataType: 'JSON',
        success: function (reponse) {
            if (reponse.success) {
                $('#fusionResultat').html(reponse.message)
                $('#fusionResultat').removeClass('d-none')
                $('#btnCheckFusion').addClass('d-none')
                $('#fusionDemande').addClass('d-none')
                $('#btnValiderFusion').removeClass('d-none')
            } else {
                alert(reponse.message)
            }
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

$('#btnValiderFusion').on('click', function (e) {
    $.ajax({
        url: '/api/fusionAdherents',
        type: 'POST',
        data: {
            idMaitre: $('#idFusionMaitre').val(),
            idEsclave: $('#idFusionEsclave').val()
        },
        dataType: 'JSON',
        success: function (reponse) {
            if (reponse.success) {
                $('#fusionResultat').html(reponse.message)
                $('#btnCancelFusion').html('Fermer')
                $('#btnValiderFusion').addClass('d-none')
            } else {
                alert(reponse.message)
            }
        },
        error: function (e) {
            let message
            try {
                const response = JSON.parse(e.responseText)
                if (response.erreur) {
                    message = response.erreur
                }
            } catch (parseError) {
                // Si le JSON est invalide ou autre erreur
                console.error('Erreur de parsing JSON :', parseError)
            }

            alert(message)
        }
    });
})


