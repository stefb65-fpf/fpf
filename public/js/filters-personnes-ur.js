//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    let term = $('.searchedTerm .value').text()
    let ur = $('.currentUr').text()
    let statut = $('select[data-ref=statut]').val()
    let typeCarte = $('select[data-ref=typeCarte]').val()
    let typeAdherent = $('select[data-ref=typeAdherent]').val()
    let url = "/urs/ur_adherents/"+ur+"/"+statut+"/"+typeCarte+"/"+typeAdherent+"/"+term;
    window.location.href = url;
})
