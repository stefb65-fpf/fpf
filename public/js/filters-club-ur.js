//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let abonnement = $('select[data-ref=abonnement]').val()
    let url = "/urs/liste_clubs/"+statut+"/"+typeCarte+"/"+abonnement;
    console.log(url)

    window.location.href = url;
})
