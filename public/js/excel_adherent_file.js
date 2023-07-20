$('#btnAdherentsList').on('click', function() {
    let club = $(this).attr('data-club')
    $('#alertAdherentsList').hide()
    $.ajax({
        url:'/api/ajax/editListAdherents',
        type: 'POST',
        data: {
            club:club,
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#linkAdherentsList').attr('href', reponse.file)
            $('#alertAdherentsList').show()
        },
        error: function (e) {
            console.log(e)
        }
    });
})
