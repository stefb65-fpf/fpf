$('#selectUrForEtiquettesContact').on('change', function() {
    $('#etiquettesContact').data('ur', $('#selectUrForEtiquettesContact option:selected').val())
})
$('a[name=editEtiquettes]').on('click', function() {
    $('a[name=viewEtiquettes]').hide()
    const elem = $(this)
    let datas = {
        ref: $(this).data('ref')
    }
    if ($(this).data('ref') === 5) {
        datas.ur = $('#etiquettesContact').data('ur')
    }
    $.ajax({
        url:'/api/editEtiquettes',
        type: 'POST',
        data: datas,
        dataType: 'JSON',
        success: function (reponse) {
            const pdf_url = $('#app_url').html() + 'storage/app/public/uploads/etiquettes/' + reponse.file
            elem.parent().parent().find('a[name=viewEtiquettes]').attr('href', pdf_url)
            elem.parent().parent().find('a[name=viewEtiquettes]').show()
            elem.parent().parent().find('a[name=viewEtiquettes]').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})

$('#btnRoutageFede').on('click', function() {
    $('#alertFedeRoutage').addClass('d-none')
    let tab = []
    $('input[name=ckbRoutageFede]').each(function() {
        if ($(this).is(':checked')) {
            tab.push($(this).data('ref'))
        }
    })
    if (tab.length === 0) {
        alert('Veuillez sélectionner au moins une liste')
        return
    }
    $('#uploaderWaiting').removeClass('d-none')
    $.ajax({
        url:'/api/editRoutageFede',
        type: 'POST',
        data: {
            tab: tab
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#uploaderWaiting').addClass('d-none')
           $('#linkFedeRoutage').attr('href', reponse.file)
            $('#alertFedeRoutage').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})
