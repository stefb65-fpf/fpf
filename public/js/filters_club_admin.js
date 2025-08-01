//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let term = $('.searchedTerm .value').text()
    let ur = $('select[data-ref=ur]').val()
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let abonnement = $('select[data-ref=abonnement]').val()

    let url = "/admin/clubs/"+ur+"/"+statut+"/"+typeCarte+"/"+abonnement+"/"+term;
    window.location.href = url;
})

//reload page  without term
$('.searchedTerm .close').on('click', function(){
    $(this).parent().parent().addClass('d-none')
    let ur = $('select[data-ref=ur]').val()
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let abonnement = $('select[data-ref=abonnement]').val()
    let url = "/admin/clubs/"+ur+"/"+statut+"/"+typeCarte+"/"+abonnement;
    window.location.href = url;
})


$('#btnExportClubByDepts').on('click', function(e) {
    $('#alertPdfClubs').addClass('d-none')
    $('#uploaderWaiting').removeClass('d-none')
    $.ajax({
        url:'/api/exportPdfClubsByDepts',
        type: 'POST',
        data: {
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#uploaderWaiting').addClass('d-none')
            $('#linkAlertPdfClubs').attr('href', reponse.file);
            $('#alertPdfClubs').removeClass('d-none')
        },
        error: function (e) {
            console.log(e)
        }
    });
})
