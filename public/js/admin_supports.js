$('a[name="prendreCharge"]').on('click', function() {
    const contenu = $(this).data('content')
    const email = $(this).data('email')
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/updateStatusSupport',
        type: 'POST',
        data: {
            ref: ref,
            status: 1
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#demandeAnswerSupport').html(contenu)
            $('#emailAnswerSupport').html(email)
            $('#sendAnswerSupport').data('ref', ref)
            tinyMCE.get('contentAnswerSupport').setContent('')
            $('#modalAnswerSupport').removeClass('d-none')
        },
        error: function (e) {
            alert('Une erreur est survenue lors de la prise en charge de la demande')
        }
    });
})

$('#cancelAnswerSupport').on('click', function() {
    $.ajax({
        url:'/api/updateStatusSupport',
        type: 'POST',
        data: {
            ref: $('#sendAnswerSupport').data('ref'),
            status: 0
        },
        dataType: 'JSON',
        success: function (reponse) {
        },
        error: function (e) {

        }
    });
})

$('#sendAnswerSupport').on('click', function() {
    const answer = tinyMCE.get('contentAnswerSupport').getContent()
    if (answer == '') {
        alert("Veuillez saisir une réponse")
        return
    }

    $.ajax({
        url:'/api/sendAnswerSupport',
        type: 'POST',
        data: {
            ref: $('#sendAnswerSupport').data('ref'),
            answer: answer
        },
        dataType: 'JSON',
        success: function (reponse) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (e) {

        }
    });
})

$('a[name="seeAnswer"]').on('click', function() {
    const contenu = $(this).data('content')
    $('#bodySeeAnswerSupport').html(contenu)
    $('#modalSeeAnswerSupport').removeClass('d-none')
})
