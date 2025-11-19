$('#typeFormation').on('change', function() {
    const type = $('#typeFormation option:selected').val()
    if (type == 0) {
        $('#divLocalisation').addClass('d-none-admin')
    } else {
        $('#divLocalisation').removeClass('d-none-admin')
    }
    display_rac()
})

$('#ur_id').on('change', function() {
    display_rac()
})

$('#numero_club').on('keyup', function() {
    display_rac()
})

function display_rac() {
    const type = $('#typeFormation option:selected').val()
    const ur_id = $('#ur_id option:selected').val()
    const numero_club = $('#numero_club').val()
    if (type == 1 && (ur_id != 0 || numero_club != '')) {
        $('#frais_deplacement_wrapper').removeClass('d-none-admin')
    } else {
        $('#frais_deplacement_wrapper').addClass('d-none-admin')
    }
    if (ur_id != 0 || numero_club != '') {
        $('#reste_a_charge_wrapper').removeClass('d-none-admin')
        $('#price_wrapper').addClass('d-none-admin')
        $('#price_not_member_wrapper').addClass('d-none-admin')
        $('#free_wrapper').removeClass('d-none-admin')
    } else {
        $('#reste_a_charge_wrapper').addClass('d-none-admin')
        $('#price_wrapper').removeClass('d-none-admin')
        $('#price_not_member_wrapper').removeClass('d-none-admin')
        $('#free_wrapper').addClass('d-none-admin')
    }
}

function recalculate_rac() {
    const cout_global = parseFloat($('#cout_global').val())
    const pec_fpf = $('#pec_fpf').val() == '' ? 0 : parseFloat($('#pec_fpf').val())
    const frais_deplacement = $('#frais_deplacement').val() == '' ? 0 : parseFloat($('#frais_deplacement').val())
    let rac = cout_global + frais_deplacement - pec_fpf
    if (rac < 0) {
        rac = 0
    }
    $('#reste_a_charge').val(rac.toString())
}
$('#pec_fpf').on('keyup', function() {
    recalculate_rac()
})
$('#frais_deplacement').on('keyup', function() {
    recalculate_rac()
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
