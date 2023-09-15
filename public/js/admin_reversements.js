$('a[name=validReversementUr]').on('click', function(e) {
    $('#montantReversementUr').html($(this).data('montant'))
    $('#urIdReversement').html($(this).data('ur'))
    $('#confirmReversementUr').data('ur', $(this).data('ur'))
    $('#confirmReversementUr').data('montant', $(this).data('montant'))
    $('#modalReversementUr').removeClass('d-none')
})
$('#confirmReversementUr').on('click', function(e) {
    const ur = $(this).data('ur')
    const montant = $(this).data('montant')
    $.ajax({
        url:'/api/reversements/confirm',
        type: 'POST',
        data: {
            ur: ur,
            montant: montant
        },
        dataType: 'JSON',
        success: function (reponse) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (e) {
            console.log(e)
        }
    });
})
