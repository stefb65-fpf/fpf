//on filter change get new url according to parameters
$('select[name=filter]').on('change',function (e) {
    e.preventDefault()
    // let term = $('.searchedTerm .value').text()
    const viewType = $('#viewType').html()
    let ur; let url;
    if (viewType === 'ur_adherents') {
        ur = $('#currentUr').text()
        url = "/urs";
    } else {
        ur = $('#urFilterPersonnes option:selected').val()
        url = "/admin";
    }
    let statut = $('#statutFilterPersonnes option:selected').val()
    let typeCarte = $('#typeCarteFilterPersonnes option:selected').val()
    let typeAdherent = $('#typeAdherentFilterPersonnes option:selected').val()

    url += "/personnes/"
    if (viewType === 'adherents') {
        url += "adherents/" + ur + "/"
    } else {
        url += "ur_adherents/"
    }
    url += statut + "/" + typeCarte + "/" + typeAdherent;
    window.location.href = url;
})

$('a[name=reEditCarte]').on('click',function (e) {
    const ref = $(this).data('ref')
    $.ajax({
        url: '/api/reEditCarte',
        type: 'POST',
        data: {
            ref: ref
        },
        success: function (data) {
            alert("La carte a été ajoutée à la liste des cartes à éditer")
        },
        error: function (err) {
            alert('Une erreur est survenue')
        }
    })
})
