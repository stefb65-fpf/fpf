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
        },
        error: function (e) {
        }
    });
})
