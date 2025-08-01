$('#selectFlorilege').on('change', function() {
    const nb = $('#selectFlorilege option:selected').val()
    const price = parseFloat($('#priceUnitFlorilege').html())
    const montant = parseInt(nb) * price
    $('#nbFlorilege').html(nb)
    $('span[name=priceFlorilege]').html(montant)
})

$('button[name=orderFlorilege]').on('click', function() {
    const type = $(this).data('type')
    const personne_id = $(this).data('personne')
    const utilisateur_id = $(this).data('identifiant')
    const nb = $('#selectFlorilege option:selected').val()
    const montant = parseFloat($('#priceUnitFlorilege').html()) * parseInt(nb)
    $.ajax({
        url: '/api/florilege/order',
        type: 'POST',
        data: {
            type: type,
            nb: nb,
            montant: montant,
            personne_id: personne_id,
            utilisateur_id: utilisateur_id
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer plus tard.')
        }
    })
})

$('button[name=orderFlorilegeClub]').on('click', function() {
    const type = $(this).data('type')
    const club_id = $(this).data('club')
    const nb = $('#selectFlorilege option:selected').val()
    const montant = parseFloat($('#priceUnitFlorilege').html()) * parseInt(nb)
    $.ajax({
        url: '/api/florilege/orderClub',
        type: 'POST',
        data: {
            type: type,
            nb: nb,
            montant: montant,
            club_id: club_id
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer plus tard.')
        }
    })
})
