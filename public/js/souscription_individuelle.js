$('button[name=btnAddIndividuel]').on('click', function(e) {
    e.preventDefault()
    const personne_id = $(this).data('personne')
    const montant = $(this).data('montant')
    const type = $(this).data('type')
    $.ajax({
        url: '/api/utilisateurs/add/individuel',
        type: 'POST',
        data: {
            montant: montant,
            personne_id: personne_id,
            type: type,
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer plus tard.')
        }
    })
})


$('button[name=btnAddAbonnement]').on('click', function(e) {
    e.preventDefault()
    const personne_id = $(this).data('personne')
    const montant = $(this).data('montant')
    const type = $(this).data('type')
    $.ajax({
        url: '/api/utilisateurs/add/abonnement',
        type: 'POST',
        data: {
            montant: montant,
            personne_id: personne_id,
            type: type,
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer plus tard.')
        }
    })
})
