$('#addInscription').on('click', function () {
    if ($('#emailAddInscription').val() == '') {
        alert('Veuillez saisir une adresse email')
        return
    }
    const regEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,10})+$/
    if (!regEmail.test($('#emailAddInscription').val())) {
        alert('Veuillez renseigner un email valide')
        return
    }
    const session = $(this).data('session')
    $.ajax({
        url: '/api/formations/addInscritToSession',
        type: 'POST',
        data: {
            email: $('#emailAddInscription').val(),
            session_id: session
        },
        success: function(data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function(err) {
            alert(err.responseJSON.erreur)
        }
    })
})

$('#addInscriptionBis').on('click', function () {
    if ($('#emailAddInscription').val() == '') {
        alert('Veuillez saisir une adresse email')
        return
    }
    const regEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,10})+$/
    if (!regEmail.test($('#emailAddInscription').val())) {
        alert('Veuillez renseigner un email valide')
        return
    }
    const session = $(this).data('session')
    $.ajax({
        url: '/api/formations/addInscritToSessionAndSendLink',
        type: 'POST',
        data: {
            email: $('#emailAddInscription').val(),
            session_id: session
        },
        success: function(data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function(err) {
            alert(err.responseJSON.erreur)
        }
    })
})
