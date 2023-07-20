//on filter change get new url according to parameters
let rootUrl = window.location.href
let club = $('.pageTitle').attr('data-club')


$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let statut = $('select[data-ref=statut]').val()
    let abonnement = $('select[data-ref=abonnement]').val()

    if(typeof club === 'undefined'){
        url = rootUrl.split('gestion_adherents')[0]+"gestion_adherents"+"/"+statut+"/"+abonnement
    }else{
        url = rootUrl.split(club)[0]+club+"/"+statut+"/"+abonnement;
    }
    window.location.href = url;
})
