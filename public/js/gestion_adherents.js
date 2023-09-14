$('#selectPays').on('change', function() {
    const indicatif = $('#selectPays option:selected').data('indicator')
    $('#indicator1').html('+' + indicatif)
    $('#indicator2').html('+' + indicatif)
})
$('#selectPaysAdresse2').on('change', function() {
    const indicatif = $('#selectPaysAdresse2 option:selected').data('indicator')
    $('#indicator3').html('+' + indicatif)
})
$('#checkBeforeInsertion').on('click', function(e) {
    e.preventDefault();
    if ($('#personneNom').val() == '') {
        alert('Veuillez renseigner le nom de la personne')
        return
    }
    if ($('#personnePrenom').val() == '') {
        alert('Veuillez renseigner le prénom de la personne')
        return
    }
    // if ($('#personneDateNaissance').val() == '') {
    //     alert('Veuillez renseigner la date de naissance de la personne')
    //     return
    // }
    if ($('#adresseCodepostal').val() == '') {
        alert('Veuillez renseigner le code postal de la personne')
        return
    }
    if ($('#adresseVille').val() == '') {
        alert('Veuillez renseigner la ville de la personne')
        return
    }
    if ($('#personneMobile').val() == '') {
        alert('Veuillez renseigner le numéro de téléphone mobile de la personne')
        return
    }
    if ($('#personneEmail').val() == '') {
        alert('Veuillez renseigner l\'email de la personne')
        return
    }
    const regEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,10})+$/
    if (!regEmail.test($('#personneEmail').val())) {
        alert('Veuillez renseigner un email valide')
        return
    }

    // on contrôle l'existence de l'email dans la BDD ou la présence d'un homonyme nom / prénom
    $.ajax({
        url: '/api/utilisateurs/checkBeforeInsertion',
        type: 'POST',
        data: {
            nom: $('#personneNom').val(),
            prenom: $('#personnePrenom').val(),
            email: $('#personneEmail').val()
        },
        success: function(data) {
            if (data.code == 10) {
                $('#nameSameEmail').html(data.personne.prenom + ' ' + data.personne.nom)
                $('#confirmSameEmail').data('personne', data.personne.id)
                $('#modalSameEmail').removeClass('d-none')
                return
            }
            if (data.code == 20) {
                $('#nameSameName').html('')
                $.each(data.personnes, function(index, personne) {
                    let chaine = '<div class="selectPersonne" data-ref="' + personne.id + '">' + personne.prenom + ' ' + personne.nom + ' - ' + personne.email + '</div>'
                    $('#nameSameName').append(chaine)
                })
                $('#modalSameName').removeClass('d-none')
                return
            }
            if (data.code == 30) {
                alert("Vous ne pouvez pas saisir une adresse email contenant le nom de domaine federation-photo.fr")
                return
            }
            if (data.code == 0) {
                $('#storeNewAdherent').submit()
                return
            }
        },
        error: function(err) {
            console.log(err)
        }
    })
})
$('#confirmSameEmail').on('click', function() {
    const personne = $(this).data('personne')
    $('#existantPersonneId').val(personne)
    $('#storeNewAdherent').submit()
})
$('#confirmSameName').on('click', function() {
    $('#storeNewAdherent').submit()
})
$(document).on('click', '.selectPersonne', function() {
    const personne = $(this).data('ref')
    $('#existantPersonneId').val(personne)
    $('#storeNewAdherent').submit()
})
