$('#checkNewUser').on('click', function () {
    $('div[name=error]').removeClass('visible')
    $('div[name=error]').html('')
    if ($('#lastnameRegister').val() == '') {
        $('#lastnameRegister').parent().find('div[name=error]').html('Veuillez saisir votre nom')
        $('#lastnameRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#firstnameRegister').val() == '') {
        $('#firstnameRegister').parent().find('div[name=error]').html('Veuillez saisir votre prénom')
        $('#firstnameRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#emailRegister').val() == '') {
        $('#emailRegister').parent().find('div[name=error]').html('Veuillez saisir votre email')
        $('#emailRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    const regEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    if (!regEmail.test($('#emailRegister').val())) {
        $('#emailRegister').parent().find('div[name=error]').html("L'email n'est pas valide")
        $('#emailRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#passwordRegister').val() == '') {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre mot de passe')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#passwordRegister').val().length < 8 || $('#passwordRegister').val().length > 30) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir entre 8 et 30 caractères')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }

    const regUpper = /^(.*[A-Z].*)+$/
    const regLower = /^(.*[a-z].*)+$/
    const regNumber = /^(.*[0-9].*)+$/
    if (!regUpper.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins une majuscule')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if (!regLower.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins une minuscule')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if (!regNumber.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins un chiffre')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }

    // tout est OK, on vérifie si l'email existe déjà en base
    $.ajax({
        url: '/api/utilisateurs/checkBeforeInsertion',
        type: 'POST',
        data: {
            email: $('#emailRegister').val(),
            nom: $('#lastnameRegister').val(),
            prenom: $('#firstnameRegister').val()
        },
        success: function (data) {
            if (data.code == 10) {
                $('#emailRegister').parent().find('div[name=error]').html('Cet email existe déjà')
                $('#emailRegister').parent().find('div[name=error]').addClass('visible')
            } else {
                // on continue l'enregistrement
                $('#checkNewUser').addClass('d-none')
                $('#registerPart2').removeClass('d-none')
            }
        },
        error: function (err) {
            console.log(err)
        }
    })
})

$('input[class=autosuggestCFA]').on('keyup', function () {
    const elem = $(this)
    const ul = $(this).parent().find('ul')
    ul.html('')
    if ($(this).val().length > 1) {
        $.ajax({
            url: '/api/ajax/getAutocompleteCommune',
            type: 'POST',
            data: {
                term: $(this).val()
            },
            dataType: 'JSON',
            success: function (data) {
                if (data.length > 0) {
                    $.each(data, function (index, value) {
                        const chaine = "<li name='communeProvided' data-id='" + value.id + "' data-name='" + value.name + "' data-zip='" + value.zip + "'>" + value.label + "</li>"
                        ul.append(chaine)
                    })
                    elem.parent().addClass('active')
                } else {
                    elem.parent().removeClass('active')
                }
            },
            error: function (e) {
            }
        });
    }
})

$(document).on('click', 'li[name=communeProvided]', function (e) {
    const commune_id = $(this).data('id')
    const commune_name = $(this).data('name')
    const commune_zip = $(this).data('zip')
    $('#codepostalRegister').val(commune_zip)
    $('#villeRegister').val(commune_name)
    $('#villeRegister').data('id', commune_id)
    $(this).parent().parent().removeClass('active')
})

$('#paysRegister').on('change', function () {
    const indicatif = $('#paysRegister option:selected').data('indicatif')
    console.log(indicatif)
    $('#indicatifRegister').html('+' + indicatif)
})

$('#checkTarifForNewUser').on('click', function () {
    const type = $(this).data('type')
    $('div[name=error]').removeClass('visible')
    $('div[name=error]').html('')
    if ($('#lastnameRegister').val() == '') {
        $('#lastnameRegister').parent().find('div[name=error]').html('Veuillez saisir votre nom')
        $('#lastnameRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#firstnameRegister').val() == '') {
        $('#firstnameRegister').parent().find('div[name=error]').html('Veuillez saisir votre prénom')
        $('#firstnameRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#emailRegister').val() == '') {
        $('#emailRegister').parent().find('div[name=error]').html('Veuillez saisir votre email')
        $('#emailRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    const regEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    if (!regEmail.test($('#emailRegister').val())) {
        $('#emailRegister').parent().find('div[name=error]').html("L'email n'est pas valide")
        $('#emailRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#passwordRegister').val() == '') {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre mot de passe')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#passwordRegister').val().length < 8 || $('#passwordRegister').val().length > 30) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir entre 8 et 30 caractères')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }

    const regUpper = /^(.*[A-Z].*)+$/
    const regLower = /^(.*[a-z].*)+$/
    const regNumber = /^(.*[0-9].*)+$/
    if (!regUpper.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins une majuscule')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if (!regLower.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins une minuscule')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if (!regNumber.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins un chiffre')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }

    if ($('#codepostalRegister').val() == '') {
        $('#codepostalRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre code postal')
        $('#codepostalRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#villeRegister').val() == '') {
        $('#villeRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre commune')
        $('#villeRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#phoneRegister').val() == '') {
        $('#phoneRegister').parent().parent().parent().find('div[name=error]').html('Veuillez saisir votre numéro de téléphone mobile')
        $('#phoneRegister').parent().parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#datenaissanceRegister').val() == '') {
        $('#datenaissanceRegister').parent().find('div[name=error]').html('Veuillez saisir votre date de naissance')
        $('#datenaissanceRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    $.ajax({
        url: '/api/utilisateurs/getTarifForNewUser',
        type: 'POST',
        data: {
            type: type,
            datenaissance: $('#datenaissanceRegister').val()
        },
        success: function (data) {
            if (data.code == 0) {
                $('#tarifAdhesion').html(data.tarif)
                $('#checkTarifForNewUser').addClass('d-none')
                $('#registerPart3').removeClass('d-none')
            } else {
                alert("L'âge indiqué est incorrect. Nous ne pouvons pas déterminer le tarif lié à votre adhésion.")
                return
            }
        },
        error: function (err) {
            console.log(err)
        }
    })
})

$('#payByVirement').on('click', function () {
    const type = $(this).data('type')
    $('div[name=error]').removeClass('visible')
    $('div[name=error]').html('')
    if ($('#lastnameRegister').val() == '') {
        $('#lastnameRegister').parent().find('div[name=error]').html('Veuillez saisir votre nom')
        $('#lastnameRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#firstnameRegister').val() == '') {
        $('#firstnameRegister').parent().find('div[name=error]').html('Veuillez saisir votre prénom')
        $('#firstnameRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#emailRegister').val() == '') {
        $('#emailRegister').parent().find('div[name=error]').html('Veuillez saisir votre email')
        $('#emailRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    const regEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    if (!regEmail.test($('#emailRegister').val())) {
        $('#emailRegister').parent().find('div[name=error]').html("L'email n'est pas valide")
        $('#emailRegister').parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#passwordRegister').val() == '') {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre mot de passe')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#passwordRegister').val().length < 8 || $('#passwordRegister').val().length > 30) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir entre 8 et 30 caractères')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }

    const regUpper = /^(.*[A-Z].*)+$/
    const regLower = /^(.*[a-z].*)+$/
    const regNumber = /^(.*[0-9].*)+$/
    if (!regUpper.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins une majuscule')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if (!regLower.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins une minuscule')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if (!regNumber.test($('#passwordRegister').val())) {
        $('#passwordRegister').parent().parent().find('div[name=error]').html('Le mot de passe doit contenir au moins un chiffre')
        $('#passwordRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }

    if ($('#codepostalRegister').val() == '') {
        $('#codepostalRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre code postal')
        $('#codepostalRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#villeRegister').val() == '') {
        $('#villeRegister').parent().parent().find('div[name=error]').html('Veuillez saisir votre commune')
        $('#villeRegister').parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#phoneRegister').val() == '') {
        $('#phoneRegister').parent().parent().parent().find('div[name=error]').html('Veuillez saisir votre numéro de téléphone mobile')
        $('#phoneRegister').parent().parent().parent().find('div[name=error]').addClass('visible')
        return
    }
    if ($('#datenaissanceRegister').val() == '') {
        $('#datenaissanceRegister').parent().find('div[name=error]').html('Veuillez saisir votre date de naissance')
        $('#datenaissanceRegister').parent().find('div[name=error]').addClass('visible')
        return
    }


    // on enregistre le paiement
    $.ajax({
        url: '/api/utilisateurs/register',
        type: 'POST',
        data: {
            type: type,
            sexe: $('input[type=radio][name=sexe]:checked').val(),
            nom: $('#lastnameRegister').val(),
            prenom: $('#firstnameRegister').val(),
            email: $('#emailRegister').val(),
            password: $('#passwordRegister').val(),
            libelle1: $('#libelle1Register').val(),
            libelle2: $('#libelle2Register').val(),
            codepostal: $('#codepostalRegister').val(),
            ville: $('#villeRegister').val(),
            pays: $('#paysRegister option:selected').val(),
            phone_mobile: $('#phoneRegister').val(),
            datenaissance: $('#datenaissanceRegister').val(),
        },
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (err) {

        }
    })
})

