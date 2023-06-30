// console.log("autocomplete")
function autocomplete (term){
    const url='/api/ajax/getAutocompleteCommune'
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            term: term
        },
        dataType: 'JSON',
        success: function (data) {
            if (data.length > 0) {
                // TODO
                console.log("success ajax")
            }
        },
        error: function (e) {
        }
    });
}

$('input[name=codepostal]').on('keyup',function(e) {
    // e.preventDefault()
    console.log($(this).val().length)
    if($(this).val().length > 1){
       autocomplete($(this).val())
    }
})
