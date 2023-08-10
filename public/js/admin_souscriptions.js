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
