$('input[name=ceFonction]').on('click', function() {
    const ref = $(this).data('ref')
    const checked = $(this).is(':checked') ? 1 : 0
    $.ajax({
        url:'/api/updateFonctionCe',
        type: 'POST',
        data: {
            ref: ref,
            ce: checked
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#infoSuccess').html("La fonction a bien été mise à jour")
            $('#infoSuccess').show()
            setTimeout(function() {
                $('#infoSuccess').hide()
            }, 1000)
        },
        error: function (e) {
        }
    });
})
$('span[name=toExpandUr]').on('click', function() {
    if ($(this).data('expand') == 0) {
        $(this).parent().parent().find('div[name=expandUr]').show()
        $(this).data('expand', 1)
        $(this).html('fermer')
    }  else {
        $(this).parent().parent().find('div[name=expandUr]').hide()
        $(this).data('expand', 0)
        $(this).html('voir')
    }
})
