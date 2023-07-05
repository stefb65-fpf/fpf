$('input[name="tarif"]').on('keydown', function(e) {
    if (e.which == 13) {
        const regNumeric = /^((?!0)\d{1,10}|0|\.\d{1,2})($|\.$|\.\d{1,2}$)/
        const tarif = $(this).val()
        if (!regNumeric.test(tarif)) {
            alert('Saisie du tarif incorrecte')
            return
        }
        const ref = $(this).data('ref')
        const statut = $(this).data('statut')
        $.ajax({
            url:'/api/ajax/updateTarif',
            type: 'POST',
            data: {
                tarif: tarif,
                ref: ref,
                statut: statut,
            },
            dataType: 'JSON',
            success: function (reponse) {
                alert(reponse.success)
            },
            error: function (e) {
            }
        });

    }
})

$('input[name="config"]').on('keydown', function(e) {
    if (e.which == 13) {
        const ref = $(this).data('ref')
        const id = $(this).data('id')
        const value = $(this).val()
        $.ajax({
            url:'/api/ajax/updateConfig',
            type: 'POST',
            data: {
                id: id,
                ref: ref,
                value: value,
            },
            dataType: 'JSON',
            success: function (reponse) {
                alert(reponse.success)
            },
            error: function (e) {
            }
        });

    }
})
