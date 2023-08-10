$('button[name=btnRenewIndividuel]').on('click', function(e) {
    e.preventDefault()
    const personne_id = $(this).data('personne')
    const montant = $(this).data('montant')
    const type = $(this).data('type')
    const adhesion = $(this).data('adhesion')
    const carte = $(this).data('carte')
    $.ajax({
        url: '/api/utilisateurs/renew/individuel',
        type: 'POST',
        data: {
            montant: montant,
            personne_id: personne_id,
            type: type,
            adhesion: adhesion,
            carte: carte,
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {
            alert('Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer plus tard.')
        }
    })
})
