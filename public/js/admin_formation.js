$('#typeFormation').on('change', function() {
    const type = $('#typeFormation option:selected').val()
    if (type == 0) {
        $('#divLocalisation').addClass('d-none-admin')
    } else {
        $('#divLocalisation').removeClass('d-none-admin')
    }
})
$('#generatePdfEvaluations').on('click', function() {
    const formation = $(this).data('formation')
    $.ajax({
        url: '/api/formations/generatePdfEvaluations',
        type: 'POST',
        data: {
            formation: formation
        },
        dataType: 'JSON',
        success: function(data) {
            $('#linkEvaluationPdf').attr('href', 'https://fpf.federation-photo.fr/storage/app/public/uploads/evaluations/' + data.year + '/' + data.file)
            $('#modalEvaluationPdf').removeClass('d-none')
        },
        error: function(err) {
            console.log(err)
        }
    })
})
