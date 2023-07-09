//listen to click on suggestion, change zip and commune fields value
function handleClick(e, suggestionDiv, divIndicator,optionPaysFrance){
    e.stopImmediatePropagation()
    if( e.target.getAttribute("name") == "suggestionItem" && $(e.target).parent()[0] == suggestionDiv[0]){
        $(suggestionDiv).parent().parent().parent().find('input[name=codepostal]').val(e.target.dataset.zip)
        $(suggestionDiv).parent().parent().parent().find('input[name=ville]').val(e.target.dataset.name)
        // console.log( $(suggestionDiv).parent().parent().parent().find('input[name=codepostal]'),$(suggestionDiv).parent().parent().parent().find('input[name=codepostal]').val(), $(suggestionDiv).parent().parent().parent().find('input[name=ville]').val())
        divIndicator.innerHTML = "+33"
        divIndicator.classList.remove('d-none')
        optionPaysFrance.value = 78
        // console.log(optionPaysFrance.value)
    }
    $('.suggestion').html("")
}
function autocomplete(term, suggestionDiv) {
    const url = '/api/ajax/getAutocompleteCommune'
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            term: term
        },
        dataType: 'JSON',
        success: function (data) {
            let divIndicator = $(suggestionDiv).parent().parent().parent().find('.indicator')[0]
            let optionPaysFrance = $(suggestionDiv).parent().parent().parent().find('select.pays')[0]
            // console.log(divIndicator, optionPaysFrance)
            if (data.length > 0) {
                //reset suggestion to empty list
                suggestionDiv.html("")
                //add each item of data to list
                data.forEach((item) => {
                    let newSuggestion = '<div class="item" name="suggestionItem" data-id="' + item.id + '" data-name="' + item.name + '" data-zip="' + item.zip + '">' + item.label + '</div>'
                    suggestionDiv.append(newSuggestion)
                })
               suggestionDiv[0].addEventListener('click', (e) => handleClick (e, suggestionDiv, divIndicator, optionPaysFrance),false )
            } else {
                $('.suggestion').html("")
                divIndicator.innerHTML = ""
                divIndicator.classList.add('d-none')
                optionPaysFrance.value = ""
            }
        },
        error: function (e) {
        }
    });
}
function handleZipAndTownDigit(e){
    let suggestionDiv = $(this).parent().find('.suggestion')
    if ($(this).val().length > 1) {
        autocomplete($(this).val(), suggestionDiv)
    }
}
$('input[name=codepostal]').bind('keyup', handleZipAndTownDigit)
$('input[name=ville]').bind('keyup',handleZipAndTownDigit)
