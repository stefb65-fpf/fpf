$(".favorite svg ").on('click', function (e) {
    const formation = $(this).parent('.favorite').data('formation')
    const elem = $(this).parent('.favorite')
    $.ajax({
        url:'/api/formations/setInterest',
        type: 'POST',
        data: {
            formation: formation
        },
        dataType: 'JSON',
        success: function () {
            elem.toggleClass('active')
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})

$('a[name=cancelInscription]').on('click', function (e) {
    const session = $(this).data('session')
    $('#confirmCancelInscription').data('ref', session)
    $('#modalCancelFormation').removeClass('d-none')
})

$('#confirmCancelInscription').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/formations/cancelInscription',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $('#modalCancelFormation').addClass('d-none')
            $('#bodyConfirmationCancelFormation').html(data.success)
            $('#modalConfirmationCancelFormation').removeClass('d-none')
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})

$('a[name=paiementInscription]').on('click', function (e) {
    const session = $(this).data('session')
    const avoir = parseFloat($(this).data('avoir'))
    const price = parseFloat($(this).data('price'))
    const real_price = price > avoir ? price - avoir : 0
    if (real_price == 0) {
        $('#paiementFormationNeeded').addClass('d-none')
        $('#paiementFormationNotNeeded').removeClass('d-none')
        $('#formationPayVirement').addClass('d-none')
        $('#formationPayCb').addClass('d-none')
        $('#saveFormationWithoutPaiement').data('ref', session)
        $('#saveFormationWithoutPaiement').removeClass('d-none')
    } else {
        $('#priceModalPaiementFormation').html(real_price + ' €')
        $('#paiementFormationNeeded').removeClass('d-none')
        $('#paiementFormationNotNeeded').addClass('d-none')
        $('#formationPayVirement').data('ref', session)
        $('#formationPayCb').data('ref', session)
        $('#formationPayVirement').removeClass('d-none')
        $('#formationPayCb').removeClass('d-none')
        $('#saveFormationWithoutPaiement').addClass('d-none')
    }

    $('#modalPaiementFormation').removeClass('d-none')
})

$('#saveFormationWithoutPaiement').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/formations/saveWithoutPaiement',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})

$('#formationPayVirement').on('click', function () {
    const ref = $(this).data('ref')
    const link = $(this).data('link')
    $.ajax({
        url:'/api/formations/payByVirement',
        type: 'POST',
        data: {
            ref: ref,
            link: link
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})

$('#formationPayCb').on('click', function () {
    const ref = $(this).data('ref')
    const link = $(this).data('link')
    $.ajax({
        url:'/api/formations/payByCb',
        type: 'POST',
        data: {
            ref: ref,
            link: link
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', data.url)
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})

$('a[name=attenteInscription]').on('click', function (e) {
    const session = $(this).data('session')
    $('#formationInscriptionAttente').data('ref', session)
    $('#modalAttenteFormation').removeClass('d-none')
})

$('#formationInscriptionAttente').on('click', function () {
    const ref = $(this).data('ref')
    $.ajax({
        url:'/api/formations/inscriptionAttente',
        type: 'POST',
        data: {
            ref: ref,
        },
        dataType: 'JSON',
        success: function (data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (e) {
            alert(e.responseJSON.erreur)
        }
    });
})


$('div[name=formateur]').on('click', function (e) {
    e.preventDefault()
    const id = $(this).data('id')
    $.ajax({
        url: '/api/getFormateur',
        type: 'POST',
        data: {id},
        dataType: 'JSON',
        success: function (reponse) {
            let chaine = ""
            if (reponse.personne.image) {
                chaine += '<img src="/storage/app/public/uploads/formateurs/' + reponse.personne.image + '"  alt="">'
            } else {
                chaine += '<img src="/storage/app/public/default_image_intervenant.png"  alt="">'
            }
            if (reponse.personne.nom != '' || reponse.personne.prenom != '') {
                chaine += '<div class="name">' + reponse.personne.prenom + ' ' + reponse.personne.nom + '</div>'
            }
            if (reponse.personne.title != '') {
                chaine += '<div class="function">' + reponse.personne.title + '</div>'
            }
            if (reponse.personne.website != '') {
                chaine += '<a class="website" target="_blank" href="' + reponse.personne.website + '">' +
                    '<div class="icon">' +
                    '<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                    '  <path d="M12.3791 0C13.5155 0 14.6115 0.146484 15.6673 0.439453C16.7231 0.732422 17.7103 1.15153 18.6291 1.69678C19.5479 2.24202 20.382 2.89307 21.1315 3.6499C21.881 4.40674 22.5258 5.25309 23.0658 6.18896C23.6057 7.12484 24.0208 8.12174 24.3109 9.17969C24.6011 10.2376 24.7502 11.3444 24.7582 12.5C24.7582 13.6475 24.6132 14.7542 24.323 15.8203C24.0329 16.8864 23.6178 17.8833 23.0779 18.811C22.5379 19.7388 21.8931 20.5811 21.1436 21.3379C20.3941 22.0947 19.5559 22.7458 18.6291 23.291C17.7023 23.8363 16.715 24.2554 15.6673 24.5483C14.6196 24.8413 13.5235 24.9919 12.3791 25C11.2427 25 10.1467 24.8535 9.09091 24.5605C8.03514 24.2676 7.04787 23.8485 6.12911 23.3032C5.21035 22.758 4.37621 22.1069 3.62669 21.3501C2.87718 20.5933 2.23243 19.7469 1.69246 18.811C1.15248 17.8752 0.737427 16.8823 0.447292 15.8325C0.157157 14.7827 0.00805932 13.6719 0 12.5C0 11.3525 0.145068 10.2458 0.435203 9.17969C0.725338 8.11361 1.14039 7.1167 1.68037 6.18896C2.22034 5.26123 2.86509 4.41895 3.6146 3.66211C4.36412 2.90527 5.20229 2.25423 6.12911 1.70898C7.05593 1.16374 8.03917 0.744629 9.07882 0.45166C10.1185 0.158691 11.2186 0.00813802 12.3791 0ZM12.3791 23.4375C13.3704 23.4375 14.3254 23.3073 15.2442 23.0469C16.163 22.7865 17.0253 22.4202 17.8312 21.9482C18.6372 21.4762 19.3706 20.9025 20.0314 20.2271C20.6923 19.5516 21.2564 18.8151 21.7239 18.0176C22.1913 17.2201 22.558 16.3493 22.824 15.4053C23.0899 14.4613 23.2189 13.4928 23.2108 12.5C23.2108 11.499 23.0819 10.5347 22.824 9.60693C22.5661 8.6792 22.2034 7.80843 21.736 6.99463C21.2685 6.18083 20.7004 5.44027 20.0314 4.77295C19.3625 4.10563 18.6331 3.53597 17.8433 3.06396C17.0535 2.59196 16.1912 2.22168 15.2563 1.95312C14.3214 1.68457 13.3623 1.55436 12.3791 1.5625C11.3878 1.5625 10.4328 1.69271 9.51402 1.95312C8.59526 2.21354 7.73291 2.57975 6.92698 3.05176C6.12105 3.52376 5.38765 4.09749 4.72679 4.77295C4.06593 5.4484 3.50177 6.1849 3.03433 6.98242C2.56689 7.77995 2.20019 8.65072 1.93424 9.59473C1.66828 10.5387 1.53933 11.5072 1.54739 12.5C1.54739 13.501 1.67634 14.4653 1.93424 15.3931C2.19213 16.3208 2.5548 17.1916 3.02224 18.0054C3.48968 18.8192 4.05787 19.5597 4.72679 20.2271C5.39571 20.8944 6.12508 21.464 6.91489 21.936C7.70471 22.408 8.56705 22.7783 9.50193 23.0469C10.4368 23.3154 11.3959 23.4456 12.3791 23.4375ZM19.5962 12.6953L20.1765 10.9375H21.0832L20.0556 14.0625H19.1489L18.5687 12.3047L17.9884 14.0625H17.0817L16.0542 10.9375H16.9608L17.5411 12.6953L18.1214 10.9375H19.016L19.5962 12.6953ZM13.9869 10.9375H14.8936L13.8661 14.0625H12.9594L12.3791 12.3047L11.7988 14.0625H10.8922L9.8646 10.9375H10.7713L11.3515 12.6953L11.9318 10.9375H12.8264L13.4067 12.6953L13.9869 10.9375ZM7.79739 10.9375H8.70406L7.6765 14.0625H6.76983L6.18956 12.3047L5.60928 14.0625H4.70261L3.67505 10.9375H4.58172L5.16199 12.6953L5.74226 10.9375H6.63685L7.21712 12.6953L7.79739 10.9375Z" fill="#2C4F80"/>\n' +
                    '</svg>'
                    +
                    '</div>Site web</a>'
            }

            if (reponse.personne.cv != '') {
                chaine += '<div class="cv">' + reponse.personne.cv + '</div>'
            }
            chaine = '<div class="modalFormateur">' + chaine + '</div>'
            $('.modalContent').html(chaine)
            $(body).addClass('modalVisible')

        }
        ,
        error: function (e) {
        }
    });
})

$('div[name=reviews]').on('click', function (e) {
    e.preventDefault()
    const id = $(this).data('id')
    $.ajax({
        url: '/api/getReviews',
        type: 'POST',
        data: {id},
        dataType: 'JSON',
        success: function (data) {
            const reviewsList = data.reviews
            let stars = '<div class="stars">' + $('div[name=reviews]').html() + '</div>'
            let reviews = ""

            if (data.nb != 0) {
                $.each(data.reviews, function (index, item) {
                    let htmlItem = ""
                    let nom = (item.prenom + item.nom).length ? item.prenom + " " + item.nom.substr(1, 1).toUpperCase() + "." : ""
                    let date = item.date.length ? item.date : ""
                    let note = item.note ? item.note : ""
                    let text = item.comment.length ? item.comment : ""

                    htmlItem +=
                        '<div class="top">' +
                        '<div class="left">' +
                        '<div class="name">' + nom + '</div>' +
                        '<div class="date">'+date+'</div>' +
                        '</div>' +
                        '<div class="right">' +
                        note +
                        '<div class="small">/5</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="bottom">' + item.comment + '</div>'

                    reviews += '<div class="reviewItem">' + htmlItem + '</div><div class="separator"></div>'
                })
                reviews = ' <div class="reviewsList">' + reviews + '</div>'
            } else {
                reviews = '<div class="noReviews">Aucun avis</div>'
            }
            let chaine = '<div class="modalReviews">' + stars + reviews + '</div>'
            $('.modalContent').html(chaine)
            $(body).addClass('modalVisible')
        }
        ,
        error: function (e) {
        }
    });

})

$('a[name=askFormation]').on('click', function (e) {
    $('#confirmAskFormation').data('level', $(this).data('level'))
    $('#modalAskFormation').removeClass('d-none')
})
$('#confirmAskFormation').on('click', function (e) {
    const level = $(this).data('level')
    const formation = $(this).data('formation')
    $.ajax({
        url: '/api/formations/askFormation',
        type: 'POST',
        data: {
            level: level,
            formation: formation
        },
        dataType: 'JSON',
        success: function (reponse) {
            $('#modalAskFormation').addClass('d-none')
            alert("Votre demande a été prise en compte")
        },
        error: function (e) {
            $('#modalAskFormation').addClass('d-none')
            alert(e.responseJSON.erreur)
        }
    })
})
