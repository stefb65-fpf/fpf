//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let term = $('.searchedTerm .value').text()
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let abonnement = $('select[data-ref=abonnement]').val()
    let url = "/urs/liste_clubs/"+statut+"/"+typeCarte+"/"+abonnement+"/"+term;
    window.location.href = url;
})

//reload page  without term
$('.searchedTerm .close').on('click', function(){
    $(this).parent().parent().addClass('d-none')
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let abonnement = $('select[data-ref=abonnement]').val()
    let url = "/urs/liste_clubs/"+statut+"/"+typeCarte+"/"+abonnement;
    window.location.href = url;
})

$('#extractClubsForUr').on('click', function(e){
    e.preventDefault()
    const statut = $('select[data-ref=statut]').val()
    const typeCarte = $('select[data-ref=typeCarte]').val()
    const abonnement = $('select[data-ref=abonnement]').val()
    const ur = $(this).data('ur')
    $.ajax({
        url: '/api/extractClubsForUr',
        type: 'POST',
        data: {
            statut: statut,
            typeCarte: typeCarte,
            abonnement: abonnement,
            ur: ur
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#uploaderWaiting').addClass('d-none')
            $('#linkAlertPdfClubs').attr('href', reponse.file);
            $('#alertPdfClubs').removeClass('d-none')
        },
        error: function (e) {
        }
    });
})
