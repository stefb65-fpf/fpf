$('#sessionPayVirement').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/sessions/payByVirement',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})

$('#sessionPayCb').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/sessions/payByCb',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})
