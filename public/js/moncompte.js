$('button[name=btnRenewIndividuel]').on('click', function(e) {
    e.preventDefault()
    const personne_id = $(this).data('personne')
    const montant = $(this).data('montant')
    const type = $(this).data('type')
    const adhesion = $(this).data('adhesion')
    const carte = $(this).data('carte')
    $.ajax({
        url: '/api/utilisateurs/renew/individuel',
        type: 'POST',
        data: {
            montant: montant,
            personne_id: personne_id,
            type: type,
            adhesion: adhesion,
            carte: carte,
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer plus tard.')
        }
    })
})

$('button[name=cancelPouvoir]').on('click', function(e) {
    $('#sentence-club').removeClass('d-none')
    $('#sentence-ur').addClass('d-none')
    $('#modalSaisieCode').addClass('d-none')
    $('#confirmSendCode').data('vote', $(this).data('vote'))
    $('#confirmSaisieCode').data('url', 'cancel')
    $('#modalVoteSendCode').removeClass('d-none')
})
$('button[name=givePouvoir]').on('click', function(e) {
    $('#sentence-ur').removeClass('d-none')
    $('#sentence-club').addClass('d-none')
    $('#modalSaisieCode').addClass('d-none')
    $('#confirmSendCode').data('vote', $(this).data('vote'))
    $('#confirmSaisieCode').data('url', 'give')
    $('#modalVoteSendCode').removeClass('d-none')
})
$('#confirmSendCode').on('click', function(e) {
    const vote = $('#confirmSendCode').data('vote')
    const moyen = $('input[type=radio][name=moyenVote]:checked').attr('value')
    $.ajax({
        url: '/api/votes/sendCode',
        type: 'POST',
        data: {
            vote: vote,
            moyen: moyen
        },
        success: function (data) {
            $('#confirmSaisieCode').data('vote', vote)
            $('#modalVoteSendCode').addClass('d-none')
            $('#modalSaisieCode').removeClass('d-none')
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'envoi de votre code. Veuillez réessayer plus tard.')
        }
    })

})
$('#confirmSaisieCode').on('click', function(e) {
    const vote = $('#confirmSaisieCode').data('vote')
    const code = $('#codeForVote').val()
    const regNum = /^[0-9]{6}$/
    if (!regNum.test(code)) {
        alert('Veuillez saisir un code numérique valide (6 chiffres)')
        return
    }
    let url
    if ($(this).data('url') == 'cancel') {
        url = '/api/votes/confirmCancelCode'
    } else {
        url = '/api/votes/confirmGiveCode'
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            vote: vote,
            code: code
        },
        success: function (data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (err) {
            alert('Une erreur est survenue lors de la validation de votre code. Veuillez réessayer plus tard.')
        }
    })
})

$('a[name=saveVote]').on('click', function(e) {
    $('#modalVoteSendCode').removeClass('d-none')
    $('#confirmSendCodeVote').data('vote', $(this).data('vote'))
    $('#confirmSendCodeVote').data('voix', $(this).data('voix'))
})
$('#confirmSendCodeVote').on('click', function(e) {
    const vote = $('#confirmSendCodeVote').data('vote')
    const moyen = $('input[type=radio][name=moyenVote]:checked').attr('value')
    $.ajax({
        url: '/api/votes/sendCode',
        type: 'POST',
        data: {
            vote: vote,
            moyen: moyen
        },
        success: function (data) {
            $('#confirmSaisieCodeVote').data('vote', vote)
            $('#confirmSaisieCodeVote').data('voix', $('#confirmSendCodeVote').data('voix'))
            $('#modalVoteSendCode').addClass('d-none')
            $('#modalSaisieCode').removeClass('d-none')
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'envoi de votre code. Veuillez réessayer plus tard.')
        }
    })
})

$('#confirmSaisieCodeVote').on('click', function(e) {
    const code = $('#codeForVote').val()
    const regNum = /^[0-9]{6}$/
    if (!regNum.test(code)) {
        alert('Veuillez saisir un code numérique valide (6 chiffres)')
        return
    }

    const vote = $(this).data('vote')
    const voix = $(this).data('voix')
    $('input[type=radio]').attr('value')
    let motions = []
    $('input[type=radio]').each(function () {
        if ($(this).is(':checked') && $(this).data('motion') == 1) {
            const reponse = $(this).attr('value')
            const tab_name = $(this).attr('name').split('_')
            const election = tab_name[1]
            motions.push({
                election: election,
                reponse: reponse
            })
        }
    })

    let candidats = []
    $('input[type=checkbox]').each(function () {
        if ($(this).is(':checked')) {
            const tab_name = $(this).attr('name').split('_')
            const election = tab_name[1]
            const candidat = $(this).attr('value')
            candidats.push({
                election: election,
                candidat: candidat
            })
        }
    })
    console.log(motions, candidats)
    $.ajax({
        url: '/api/votes/saveVote',
        type: 'POST',
        data: {
            vote: vote,
            voix: voix,
            motions: motions,
            candidats: candidats,
            code: code
        },
        success: function (data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (err) {
            alert('Une erreur est survenue lors de la validation de votre code. Veuillez réessayer plus tard.')
        }
    })
})
