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
        alert('Veuillez saisir une information de paiement')
        return
    }
    const id = $(this).data('id')
    $.ajax({
        url:'/api/validReglement',
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
        url:'/api/editCartes',
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
//reload page  without term
$('.searchedTerm .close').on('click', function(){
    $(this).parent().parent().addClass('d-none')
    let url = "/admin/reglements";
    window.location.href = url;
})

$('a[name=relanceMail]').on('click', function(e){
    $('#refRelance').html($(this).data('reference'))
    $('#validRelance').data('id', $(this).data('id'))
    $('#modalRelanceMail').removeClass('d-none')
})
$('#validRelance').on('click', function(e){
    const id = $(this).data('id')
    $.ajax({
        url:'/api/relanceReglement',
        type: 'POST',
        data: {
            ref: id
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalRelanceMail').addClass('d-none')
            $('#modalRelanceOk').removeClass('d-none')
        },
        error: function (e) {
            alert("Une erreur s'est produite")
        }
    });
})
