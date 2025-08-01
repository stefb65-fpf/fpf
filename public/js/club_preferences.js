function submitNewsPreferences(preference, clubId, form, clickedElement) {
    let url = ""
    if (form === "activites") {
        url = '/api/submitClubActivites'
    }
    if (form === "equipements") {
        url = '/api/submitClubEquipements'
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            clubPreferences: preference,
            club: clubId
        },
        dataType: 'JSON',
        success: function (data) {
            if (data.length > 0) {
                // console.log(data[0]= true)
                clickedElement.parent().parent().find('.message').addClass('show')
                setTimeout(() => {
                    clickedElement.parent().parent().find('.message').removeClass('show')
                }, "3000")
            }
        },
        error: function (e) {
        }
    });
}

$('div[name=ajaxCheckbox]').on('click', function (e) {
    let clubId = $(this).parent().data("club")
    let form = $(this).parent().data("form")
    let activite = $(this).find('input').val()
    // console.log(clubId, form,activite,$(this))
    if (!(e.target == $(this).find("input")[0])) {
        //clic ailleurs que sur l'input
        if ($(this).find("input")[0].checked == true) {
            $(this).find("input").prop('checked', false)
        } else {
            $(this).find("input").prop('checked', true)
        }
    }
    submitNewsPreferences(activite, clubId, form, $(this))
})

$('input[name=affichage_photo_club]').on('click', function (e) {
    $('#messageAffichagePhoto').removeClass('show')
    const ref = $(this).data('ref')
    let affichage = $(this).is(':checked') ? 1 : 0
    $.ajax({
        url: '/api/updateAffichagePhotoClub',
        type: 'POST',
        data: {
            ref: ref,
            affichage: affichage
        },
        dataType: 'JSON',
        success: function (data) {
            $('#messageAffichagePhoto').addClass('show')
        },
        error: function (e) {
        }
    });
})


$('#askClosed').on('click', function (e) {
    $('#modalConfirmClosed').removeClass('d-none')
})

$('#confirmClosedClub').on('click', function (e) {
    const ref = $(this).data('club')
    $.ajax({
        url: '/api/updateClosedClub',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            alert('Le club a été déclaré fermé avec succès.');
            $('#modalConfirmClosed').addClass('d-none')
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (e) {
            alert('Une erreur est survenue lors de la déclaration du club comme fermé.');
        }
    });
})

