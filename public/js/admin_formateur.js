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

$('a[name=photoFormateur]').on('click', function () {
    uploader.settings.url = $(this).data('url')
    $('#browse').trigger('click')
})

let uploader = new plupload.Uploader({
    runtimes : 'html5',
    browse_button : 'browse',
    drop_element: 'browse',
    url: $('#browse').data('url'),
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    init: {
        PostInit: function() {
            // $('#ajaxLoaderElement').hide();
            // $('#textUploadElement').show();
        },
        FilesAdded: function(up, files) {
            uploader.start();
            $('#uploaderWaiting').removeClass('d-none')
        },
        UploadProgress: function(up, file) {
        },

//    , si le code est 20 ou 30, on affiche un message pour dire que ce n'est pas OK.
        FileUploaded: function(up, file, reponse) {
            let i = 1;
            $.each(reponse, function(data, value){
                $('#uploaderWaiting').addClass('d-none')
                if (i === 1) {
                    const datarep = $.parseJSON(value)
                    if (datarep.success) {
                        $(location).attr('href', $(location).attr('href'))
                    } else {
                        alert(datarep.message)
                    }
                }
                i++;
            })
        },
    }
});
uploader.init();
