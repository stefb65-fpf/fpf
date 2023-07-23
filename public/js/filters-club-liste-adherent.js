//on filter change get new url according to parameters
let rootUrl = window.location.href
let club = $('.pageTitle').attr('data-club')


$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let statut = $('select[data-ref=statut]').val()
    let abonnement = $('select[data-ref=abonnement]').val()

    if(typeof club === 'undefined'){
        url = rootUrl.split('gestion_adherents')[0]+"gestion_adherents"+"/"+statut+"/"+abonnement
    }else{
        url = rootUrl.split(club)[0]+club+"/"+statut+"/"+abonnement;
    }
    window.location.href = url;
})


$('select[name=selectCt]').on('change',function (e) {
    if ($(this).val() == 5 || $(this).val() == 6) {
        $(this).parent().find('div[name=divSecondeCarte]').removeClass('d-none')
    } else {
        $(this).parent().find('div[name=divSecondeCarte]').addClass('d-none')
    }
})

$('input[name=adherer]').on('click',function (e) {
    if($(this).is(':checked')) {
        $('#renouvellementAdherents').removeAttr('disabled')
    } else {
        let selected = false
        $('input[name=adherer]').each(function () {
            if ($(this).is(':checked')) {
                selected = true
            }
        })
        $('input[name=abonner]').each(function () {
            if ($(this).is(':checked')) {
                selected = true
            }
        })
        if (!selected) {
            $('#renouvellementAdherents').attr('disabled', 'disabled')
        }
    }
})
$('input[name=abonner]').on('click',function (e) {
    if($(this).is(':checked')) {
        $('#renouvellementAdherents').removeAttr('disabled')
    } else {
        let selected = false
        $('input[name=adherer]').each(function () {
            if ($(this).is(':checked')) {
                selected = true
            }
        })
        $('input[name=abonner]').each(function () {
            if ($(this).is(':checked')) {
                selected = true
            }
        })
        if (!selected) {
            $('#renouvellementAdherents').attr('disabled', 'disabled')
        }
    }
})

$('#renouvellementAdherents').on('click',function (e) {
    // on récupère tous les id des adhérents sélectionnés
    let idAdherents = []
    const regIdentifiant = /^[0-9]{2}-[0-9]{4}-[0-9]{4}$/
    $('input[name=adherer]').each(function () {
        const item = $(this)
        if (item.is(':checked')) {
            // on contrôle que le ct et la seconde carte sont bien renseignés
            const ref = item.data('ref')
            if ($('#selectCt_' + ref).val() == 5 || $('#selectCt_' + ref).val() == 6) {
                if ($('#secondeCarte_' + ref).val() == '') {
                    alert('Veuillez renseigner le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant'))
                    return
                }
                if (!regIdentifiant.test($('#secondeCarte_' + ref).val())) {
                    alert('le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant') + ' est incorrect')
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
    })
    let idAbonnes = []
    $('input[name=abonner]').each(function () {
        if ($(this).is(':checked')) {
            idAbonnes.push($(this).data('ref'))
        }
    })
    const aboClub = $('#abonnementClub').is(':checked') ? 1 : 0
    $('#renouvellementListe').html('')
    $.ajax({
        url:'/api/ajax/checkRenouvellementAdherents',
        type: 'POST',
        data: {
            adherents: idAdherents,
            abonnes: idAbonnes,
            club: $('#renouvellementAdherents').data('club'),
            aboClub: aboClub
        },
        dataType: 'JSON',
        success: function (reponse) {
            $.each(reponse.adherents, function (index, item) {
                let chaine = '<div class="d-flex w100 justify-around line-bordereau">'
                chaine += '<div class="flex-2 small">' +  item.adherent.identifiant + ' ' + item.adherent.nom + ' ' + item.adherent.prenom + '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.adherent.ct !== 'undefined' ? item.adherent.ct : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.adhesion !== 'undefined' ? item.adhesion + '€' : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">'
                chaine += typeof item.abonnement !== 'undefined' ? item.abonnement + '€' : ''
                chaine += '</div>'
                chaine += '<div class="flex-1 small">' + item.total + '€</div>'
                chaine += '</div>'
                $('#renouvellementListe').append(chaine)
            })
            if (reponse.montant_abonnement_club != 0 || reponse.montant_adhesion_club != 0) {
                if (reponse.montant_abonnement_club != 0) {
                    $('#montantClubAbonnement').html(reponse.montant_abonnement_club)
                    $('#divRenouvellementAbonnementClub').removeClass('d-none')
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
            $('#montantRenouvellement').html(reponse.total_montant)
            $('#modalRenouvellement').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

$('#btnRenouvellement').on('click',function (e) {
    // on valide les données saisies
    let idAdherents = []
    const regIdentifiant = /^[0-9]{2}-[0-9]{4}-[0-9]{4}$/
    $('input[name=adherer]').each(function () {
        const item = $(this)
        if (item.is(':checked')) {
            // on contrôle que le ct et la seconde carte sont bien renseignés
            const ref = item.data('ref')
            if ($('#selectCt_' + ref).val() == 5 || $('#selectCt_' + ref).val() == 6) {
                if ($('#secondeCarte_' + ref).val() == '') {
                    alert('Veuillez renseigner le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant'))
                    return
                }
                if (!regIdentifiant.test($('#secondeCarte_' + ref).val())) {
                    alert('le numéro de la seconde carte pour l\'adhérent ' + item.data('identifiant') + ' est incorrect')
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
    })
    let idAbonnes = []
    $('input[name=abonner]').each(function () {
        if ($(this).is(':checked')) {
            idAbonnes.push($(this).data('ref'))
        }
    })
    const aboClub = $('#abonnementClub').is(':checked') ? 1 : 0
    $.ajax({
        url:'/api/ajax/validRenouvellementAdherents',
        type: 'POST',
        data: {
            adherents: idAdherents,
            abonnes: idAbonnes,
            club: $('#renouvellementAdherents').data('club'),
            aboClub: aboClub
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalRenouvellement').addClass('d-none')
            $('#lienBordereauClub').attr('href', $('#app_url').html() + reponse.file)
            $('#modalRenouvellementOk').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

