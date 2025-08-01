$('#btnSouscriptionsList').on('click', function () {
    $('#alertFlorilege').addClass('d-none')
    $.ajax({
        url:'/api/generateSouscriptionsList',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#linkAlertFlorilege').attr('href', reponse.file)
            $('#alertFlorilege').removeClass('d-none')
        },
        error: function (e) {
            console.log(e)
        }
    });
})

$('#btnSouscriptionsColisage').on('click', function () {
    $('#alertFlorilege').addClass('d-none')
    $('#uploaderWaiting').removeClass('d-none')
    $.ajax({
        url:'/api/generateSouscriptionsColisage',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#uploaderWaiting').addClass('d-none')
            $('#linkAlertFlorilege').attr('href', reponse.file)
            $('#alertFlorilege').removeClass('d-none')
        },
        error: function (e) {
            console.log(e)
        }
    });
})

$('#btnSendSouscriptionsColisage').on('click', function () {
    $('#alertFlorilege').addClass('d-none')
    $('#uploaderWaiting').removeClass('d-none')
    $.ajax({
        url:'/api/sendSouscriptionsColisage',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#uploaderWaiting').addClass('d-none')
            alert('les listes de colisage ont été transmises aux clubs et Urs')
            // $('#linkAlertFlorilege').attr('href', reponse.file)
            // $('#alertFlorilege').removeClass('d-none')
        },
        error: function (e) {
            console.log(e)
        }
    });
})

$('#btnSouscriptionsColisageRouteur').on('click', function () {
    $('#alertFlorilege').addClass('d-none')
    $.ajax({
        url:'/api/generateSouscriptionsColisageRouteur',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#linkAlertFlorilege').attr('href', reponse.file)
            $('#alertFlorilege').removeClass('d-none')
        },
        error: function (e) {
            console.log(e)
        }
    });
})
