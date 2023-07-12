//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let ur = $('select[data-ref=ur]').val()
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let abonnement = $('select[data-ref=abonnement]').val()
    let url = "/admin/clubs/"+ur+"/"+statut+"/"+typeCarte+"/"+abonnement;
    window.location.href = url;
})
