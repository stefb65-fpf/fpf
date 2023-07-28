$('#paysContact').on('change', function () {
    const indicatif = $('#paysContact option:selected').data('indicatif')
    $('div[name=indicatifContact]').html('+' + indicatif)
})
$('#paysClub').on('change', function () {
    const indicatif = $('#paysClub option:selected').data('indicatif')
    $('div[name=indicatifClub]').html('+' + indicatif)
})

// $('#saveClubByAdmin').on('click', function (e) {
//     e.preventDefault()
//
// })
