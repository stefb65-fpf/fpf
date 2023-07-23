$('.modalEditClose').on('click', function(e){
    e.preventDefault()
    $(this).parent().parent().addClass('d-none')
})
$('.modalEditCloseReload').on('click', function(e){
    e.preventDefault()
    $(location).attr('href', $(location).attr('href'))
})
