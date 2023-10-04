$('#typeFormation').on('change', function() {
    const type = $('#typeFormation option:selected').val()
    if (type == 0) {
        $('#divLocalisation').addClass('d-none-admin')
    } else {
        $('#divLocalisation').removeClass('d-none-admin')
    }
})
