$('.modalEditClose').on('click', function(e){
    e.preventDefault()
    $(this).parent().parent().addClass('d-none')
})
