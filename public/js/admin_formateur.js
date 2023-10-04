$('#searchForAddingTrainer').on('click', function () {
    if ($('#trainerEmail').val() == '') {
        alert('Veuillez saisir un email')
        return
    }
    const email = $('#trainerEmail').val()
    $.ajax({
        url:'/api/checkTrainerEmail',
        type: 'POST',
        data: {
            email: email
        },
        dataType: 'JSON',
        success: function (reponse) {
            if (reponse.success) {
                $(location).attr('href', reponse.link)
            } else {
                alert(reponse.error)
            }
        },
        error: function (e) {
            alert("une erreur est survenue lors de la recherche de l'email")
        }
    });
})
