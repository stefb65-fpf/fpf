//searchbox topbar
$('.icon[name=searchBtn]').on("click", function () {
    $(this).parent().toggleClass("active")
    $('.searchContainer').toggleClass('searching')
    $(this).parent().parent().find('input').trigger( "focus" )
})

function searchClub(term) {
    const url = '/api/isAdmin'
    $.ajax({
        url: url,
        dataType: 'JSON',
        success: function (data) {
            let target=""
            if (data.isAdmin) {
                target =  "/admin/clubs/all/all/all/all/"+term;
            } else {
                target = "/urs/liste_clubs/all/all/all/"+term;
            }
            window.location.href = target;
        },
        error: function (e) {
            console.log(e)
        }
    });
}
function searchPerson(term){
    const url = '/api/isAdmin'
    $.ajax({
        url: url,
        dataType: 'JSON',
        success: function (data) {
            let target=""
            if (data.isAdmin) {
                target =  "/admin/personnes/recherche/all/all/all/all/"+term;
            } else {
                target = "/urs/personnes/recherche/all/all/all/"+term;
            }
            window.location.href = target;
        },
        error: function (e) {
            console.log(e)
        }
    });
}
function searchPayment(term) {
    const url = '/admin/reglements/'+term
    window.location.href = url;

}
$('.searchBox.club input').on('keypress', function (e) {
    if (e.which === 13) {
        searchClub($(this).val());
    }
})

$('.searchBox.person input').on('keypress', function (e) {
    if (e.which === 13) {
        searchPerson($(this).val());
    }
})
$('.searchBox.payment input').on('keypress', function (e) {
    if (e.which === 13) {
        searchPayment($(this).val());
    }
})

