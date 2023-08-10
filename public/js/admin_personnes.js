$('#paysPersonne').on('change', function () {
    const indicatif = $('#paysPersonne option:selected').data('indicatif')
    $('#indicatifMobile').html('+' + indicatif)
    $('#indicatifDomicile').html('+' + indicatif)
})

$('#paysPersonneLivraison').on('change', function () {
    const indicatif = $('#paysPersonneLivraison option:selected').data('indicatif')
    $('#indicatifDomicileLivraison').html('+' + indicatif)
})
