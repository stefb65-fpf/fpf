$('#clubPayVirement').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/clubs/payByVirement',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (e) {
        }
    });
})

$('#clubPayCb').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/clubs/payByCb',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (e) {
            alert('Une erreur est survenue, veuillez réessayer plus tard')
        }
    });
})
