
function submitNewsPreferences(preference, clubId, form,clickedElement){
    let url= ""
    if(form === "activites"){
        url = '/api/ajax/submitClubActivites'
    }
    if(form === "equipements"){
        url = '/api/ajax/submitClubEquipements'
    }
    $.ajax({
        url:url,
        type: 'POST',
        data: {
            clubPreferences: preference,
            club: clubId
        },
        dataType: 'JSON',
        success: function (data) {
            if (data.length > 0) {
                // console.log(data[0]= true)
                clickedElement.parent().parent().find('.message').addClass('show')
                setTimeout(()=> {
                    clickedElement.parent().parent().find('.message').removeClass('show')
                }, "3000")
            }
        },
        error: function (e) {
        }
    });
}

$('div[name=ajaxCheckbox]').on('click',function(e){
        let clubId = $(this).parent().data("club")
        let form = $(this).parent().data("form")
        let activite = $(this).find('input').val()
    // console.log(clubId, form,activite,$(this))
    if(!(e.target ==   $(this).find("input")[0])){
        //clic ailleurs que sur l'input
        if($(this).find("input")[0].checked == true){
            $(this).find("input").prop('checked', false)
        }else{
            $(this).find("input").prop('checked', true)
        }
    }
        submitNewsPreferences(activite, clubId, form, $(this))
    })

