//searchbox topbar
$('.icon[name=searchBtn]').on("click", function () {
    $(this).parent().toggleClass('active')
    $('.searchContainer').toggleClass('searching')
    $(this).parent().parent().find('input').trigger( "focus" )
})

function searchClub(term) {
    const url = '/api/ajax/isAdmin'
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
// console.log(target)
            window.location.href = target;
        },
        error: function (e) {
            console.log(e)
        }
    });
}
$('.searchBox.club input').on('keypress', function (e) {
    // e.preventDefault()
    if (e.which === 13) {
        searchClub($(this).val());
    }
})

