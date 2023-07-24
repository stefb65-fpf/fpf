$('a[name=validerReglement]').on('click', function(e){
    e.preventDefault()
    let id = $(this).data('id')
    let reference = $(this).data('reference')
    let montant = $(this).data('montant')
    $('#referenceValidationRenouvellement').html(reference)
    $('#montantValidationRenouvellement').html(montant)
    $('#validRenouvellement').data('id', id)
    console.log(id)
    $('#modalValidationRenouvellement').removeClass('d-none')
})
$('#validRenouvellement').on('click', function(e){
    if ($('#infoValidationRenouvellement').val() == '') {
        alert('Veuillez saisir une informationd e paiement')
        return
    }
    const id = $(this).data('id')
    $.ajax({
        url:'/api/ajax/validReglement',
        type: 'POST',
        data: {
            ref: id,
            infos: $('#infoValidationRenouvellement').val()
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalValidationRenouvellement').addClass('d-none')
            $('#modalReglementOk').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

$('#editerCartes').on('click', function(e){
    e.preventDefault()
    $.ajax({
        url:'/api/ajax/editCartes',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            let chaine = ''
            if (reponse.file_cartes != '') {
                chaine += '<li><a target="_blank" href="' + $('#app_url').html() + reponse.file_cartes + '">le fichier des cartes</a></li>'
            }
            if (reponse.file_etiquettes_club != '') {
                chaine += '<li><a target="_blank" href="' + $('#app_url').html() + reponse.file_etiquettes_club + '">le fichier liste pour les clubs</a></li>'
            }
            if (reponse.file_etiquettes_individuels != '') {
                chaine += '<li><a target="_blank" href="' + $('#app_url').html() + reponse.file_etiquettes_individuels + '">le fichier des étiquettes pour les individuels</a></li>'
            }

            $('#listeCartesEditees').html(chaine)
            $('#modalCartesOk').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})
