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
        url:'/api/ajax/editEtiquettes',
        type: 'POST',
        data: datas,
        dataType: 'JSON',
        success: function (reponse) {
            const pdf_url = 'https://fpf-new.federation-photo.fr/storage/app/public/uploads/etiquettes/' + reponse.file
            elem.parent().parent().find('a[name=viewEtiquettes]').attr('href', pdf_url)
            elem.parent().parent().find('a[name=viewEtiquettes]').show()
        },
        error: function (e) {
        }
    });
})

$('#btnRoutageFede').on('click', function() {
    $('#alertFedeRoutage').hide()
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
    $.ajax({
        url:'/api/ajax/editRoutageFede',
        type: 'POST',
        data: {
            tab: tab
        },
        dataType: 'JSON',
        success: function (reponse) {
           $('#linkFedeRoutage').attr('href', reponse.file)
            $('#alertFedeRoutage').show()
        },
        error: function (e) {
        }
    });
})
