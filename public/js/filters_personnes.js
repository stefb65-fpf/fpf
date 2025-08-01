//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    // let term = $('.searchedTerm .value').text()
    const viewType = $('#viewType').html()
    let ur; let url;
    if (viewType === 'ur_adherents') {
        ur = $('#currentUr').text()
        url = "/urs";
    } else {
        ur = $('#urFilterPersonnes option:selected').val()
        url = "/admin";
    }
    let statut = $('#statutFilterPersonnes option:selected').val()
    let typeCarte = $('#typeCarteFilterPersonnes option:selected').val()
    let typeAdherent = $('#typeAdherentFilterPersonnes option:selected').val()
    let anciennete = $('#ancienneteFilterPersonnes option:selected').val()

    url += "/personnes/"
    if (viewType === 'adherents') {
        url += "adherents/" + ur + "/"
    } else {
        url += "ur_adherents/"
    }
    url += statut + "/" + typeCarte + "/" + typeAdherent;
    if (anciennete === '1') {
        url += "/null/1";
    }
    window.location.href = url;
})

$('a[name=reEditCarte]').on('click',function (e) {
    const ref = $(this).data('ref')
    $.ajax({
        url: '/api/reEditCarte',
        type: 'POST',
        data: {
            ref: ref
        },
        success: function (data) {
            alert("La carte a été ajoutée à la liste des cartes à éditer")
        },
        error: function (err) {
            alert('Une erreur est survenue')
        }
    })
})

$('a[name=renewIndividuel]').on('click',function (e) {
    const ct = $(this).data('ct')
    if (ct == 7) {
        $('input[name=aboIndividuel]').attr('disabled', true)
    } else {
        $('input[name=aboIndividuel]').removeAttr('disabled')
    }
    if (ct == 'F') {
        $('#premiereCarteRenewIndividuel').removeAttr('disabled')
    } else {
        $('#premiereCarteRenewIndividuel').attr('disabled', true)
    }
    $('#confirmRenewIndividuel').data('identifiant', $(this).data('ref'))
    $('input[type=radio][name=adhesionIndividuel][value=' + ct + ']').prop('checked', true)
    $('input[type=radio][name=adhesionIndividuel][value=' + ct + ']').attr('checked', 'checked')
    $('#modalRenewIndividuel').removeClass('d-none')
})
$('input[type=radio][name=adhesionIndividuel]').on('change', function (e) {
    if ($(this).val() == 7) {
        $('input[name=aboIndividuel]').attr('disabled', true)
        $('input[type=radio][name=aboIndividuel][value=1]').prop('checked', true)
        $('input[type=radio][name=aboIndividuel][value=1]').attr('checked', 'checked')
    } else {
        $('input[name=aboIndividuel]').removeAttr('disabled')
    }
    if ($(this).val() == 'F') {
        $('#premiereCarteRenewIndividuel').removeAttr('disabled')
    } else {
        $('#premiereCarteRenewIndividuel').attr('disabled', true)
    }
})

$('#confirmRenewIndividuel').on('click', function (e) {
    const identifiant = $(this).data('identifiant')
    const adhesion = $('input[type=radio][name=adhesionIndividuel]:checked').val()
    const abo = $('input[type=radio][name=aboIndividuel]:checked').val()
    const premiereCarte = $('#premiereCarteRenewIndividuel').val()
    if (adhesion == 'F') {
        if (premiereCarte == '' || premiereCarte.length !== 12) {
            alert('Veuillez saisir un numéro valide pour la première carte')
            return false
        }
    }
    $.ajax({
        url: '/api/admin/renew/individuel',
        type: 'POST',
        data: {
            identifiant: identifiant,
            adhesion: adhesion,
            abo: abo,
            premiereCarte: premiereCarte
        },
        success: function (data) {
            alert("La demande de renouvellement a été enregistrée. Vous pouvez valider le règlement " + data.ref)
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (err) {
            alert('Une erreur est survenue lors du renouvellement ' + err.responseJSON.erreur)
        }
    })
})
