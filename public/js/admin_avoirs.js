$('a[name=remboursementAvoir]').on('click', function(e){
    $('#btnValidateRemboursementAvoir').data('type', $(this).data('type'))
    $('#btnValidateRemboursementAvoir').data('ref', $(this).data('ref'))
    $('#modalRemboursementAvoir').removeClass('d-none')
})

$('#btnValidateRemboursementAvoir').on('click', function(e){
    const type = $(this).data('type')
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/cancelAvoir',
        type: 'POST',
        data: {
            ref: ref,
            type: type
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalRemboursementAvoir').addClass('d-none')
            alert("Le remboursement de l'avoir a été pris en compte et la créance annulée.")
            window.location.reload()
        },
        error: function (e) {
            alert("Une erreur s'est produite")
        }
    });
})
