let navbar = document.querySelector(".navbar")
let hamburger = document.querySelector('.hamburgerIcon')
// let autosuggestContainer = document.querySelector(".autosuggestContainer")
// let autosuggestCFA = document.querySelector(".autosuggestCFA")
let body = document.querySelector('body')

const navbarManagement = function () {
    navbar.classList.toggle('hidden')
    hamburger.classList.toggle('close')
}
if (window.innerWidth < 992 && hamburger) {
    hamburger.addEventListener("click", navbarManagement)
}
window.addEventListener("resize", () => {
    //hamburger management
    if (hamburger) {
        if (window.innerWidth < 992) {
            navbar.classList.add('hidden')
            hamburger.classList.remove('close')
            hamburger.addEventListener("click", navbarManagement)
        } else {
            navbar.classList.remove('hidden')
            hamburger.removeEventListener("click", navbarManagement)
        }
    }
})

// form show password
let formIcon = document.querySelector(".customField .icons.eye")
let formIcons = document.querySelectorAll(".customField .icons.eye")
formIcons.forEach((formIcon) => {
    if (formIcon) {
        formIcon.addEventListener('click', () => {
            formIcon.querySelector('.open').classList.toggle('hidden')
            formIcon.querySelector('.closed').classList.toggle('hidden')
            let inputPassword = formIcon.closest(".customField").querySelector('input')
            if (inputPassword.type == "password") {
                inputPassword.type = "text"
            } else {
                inputPassword.type = "password"
            }
        })
    }
})

//search in topBar
let searchIconBtns = document.querySelectorAll(".searchItem .icon .iconBtn")
let searchItems = document.querySelectorAll(".searchItem")
let searchBox = document.querySelector(".searchContainer")
if (searchBox) {
    let target = 0
    searchBox.addEventListener('click', (e) => {
        let isBtnClicked = false
        let isSearching = false
        searchIconBtns.forEach((searchIconBtn) => {
            if (e.target.dataset.target == searchIconBtn.dataset.target) {
                target = searchIconBtn.dataset.target
                isBtnClicked = true
            }
        })
        searchItems.forEach((searchItem) => {
            if (isBtnClicked) {
                if (target == searchItem.dataset.target) {
                    searchItem.classList.toggle("active")
                    if (searchItem.classList.contains("active")) {
                        searchItem.querySelector("input").focus()
                        isSearching = true
                    } else {
                        isSearching = false
                    }
                } else {
                    searchItem.classList.remove("active")
                }
            }
        })
        if (isSearching) {
            searchBox.classList.add("searching")
        } else {
            searchBox.classList.remove("searching")
        }
    })
}

//account profile
$('button[name=updateForm]').on('click', function (e) {
    e.preventDefault()
    let formIdName = $(this).parent().attr('data-formId')
    $(this).addClass('d-none')
    $(this).parent().find('button[name=enableBtn]').removeClass('d-none')
    $('#' + formIdName).find('.formValue').removeAttr('disabled').addClass('modifying')
    // $(this).parent().parent().find('.formValue').removeAttr('disabled').addClass('modifying')
    $('#' + formIdName).find('.modifyVisible').removeClass('modifyVisible')

    if ($(this).hasClass('showFields')) {
        $('#' + formIdName).find('.hiddenFields').removeClass('hidden')
        // $(this).parent().parent().find('.hiddenFields').removeClass('hidden')
    }
})
//send form on click button[name=enableBtn]
$('button[name=enableBtn]').on('click', function (e) {
    e.preventDefault()
    let formIdName = $(this).parent().attr('data-formId')
    // console.log(   $('#'+ formIdName))
    $('#' + formIdName).submit()
})

//check password format in reinit password
let checkableWidth = document.querySelector(".instructions .list .item.width")
let checkableSmallLetter = document.querySelector(".instructions .list .item.smallLetter")
let checkableupperCaseLetter = document.querySelector(".instructions .list .item.upperCaseLetter")
let checkableNumber = document.querySelector(".instructions .list .item.number")
let checkableConfirmation = document.querySelector(".instructions .list .item.confirmation")

let checkableBtnOriginal = document.querySelector(".checkableInput.original")
let checkableBtnConfirmation = document.querySelector(".checkableInput.confirmation")
let submitBtn = document.querySelector("#resetPasswordBtn")

if (checkableBtnOriginal) {
    let length = false;
    let lower = false;
    let uppercase = false;
    let number = false;
    let confirmation = false;
    checkableBtnOriginal.addEventListener("keyup", (e) => {
        //verif de la confirmation
        let password = checkableBtnOriginal.value
        if (password === checkableBtnConfirmation.value && checkableBtnOriginal.value.length) {
            checkableConfirmation.classList.add("ok")
            confirmation = true
        } else {
            checkableConfirmation.classList.remove("ok")
            confirmation = false
        }
        //verif des regex et longueur
        if (checkableBtnOriginal.value.length > 7 && checkableBtnOriginal.value.length < 36) {
            checkableWidth.classList.add("ok")
            length = true
        } else {
            checkableWidth.classList.remove("ok")
            length = false
        }
        const regUpper = /^(.*[A-Z].*)+$/
        if (regUpper.test(password)) {
            checkableupperCaseLetter.classList.add("ok")
            uppercase = true

        } else {
            checkableupperCaseLetter.classList.remove("ok")
            uppercase = false
        }
        const regLower = /^(.*[a-z].*)+$/
        if (regLower.test(password)) {
            checkableSmallLetter.classList.add("ok")
            lower = true
        } else {
            checkableSmallLetter.classList.remove("ok")
            lower = false
        }
        const regNumber = /^(.*[0-9].*)+$/
        if (regNumber.test(password)) {
            checkableNumber.classList.add("ok")
            number = true
        } else {
            checkableNumber.classList.remove("ok")
            number = false
        }
        if (confirmation && number && length && lower && uppercase) {
            submitBtn.removeAttribute("disabled")
        } else {
            submitBtn.setAttribute("disabled", "")
        }
    });
    checkableBtnConfirmation.addEventListener("keyup", (e) => {
        //verif de la confirmation
        if (checkableBtnOriginal.value === checkableBtnConfirmation.value && checkableBtnConfirmation.value.length) {
            checkableConfirmation.classList.add("ok")
            confirmation = true
        } else {
            checkableConfirmation.classList.remove("ok")
            confirmation = false
        }
        if (confirmation && number && length && lower && uppercase) {
            submitBtn.removeAttribute("disabled")
        } else {
            submitBtn.setAttribute("disabled", "")
        }
    });

}
//modal
let modalBackground = document.querySelector(".modalBackground")
let modalContent = document.querySelector(".modalContent")
let modalClose = document.querySelector(".modalWrapper .close .clickable")
let modalWrapper = document.querySelector(".modalWrapper")
let modalTriggers = document.querySelectorAll(".modalTrigger")

if (modalBackground) {
    modalBackground.addEventListener('click', (e) => {
        // e.preventDefault()
        if ((e.target == modalBackground && e.target !== modalWrapper) || e.target == modalClose) {
            body.classList.remove("modalVisible")
        }
    })
}
if (modalTriggers.length) {
    modalTriggers.forEach((trigger) => {
        trigger.addEventListener('click', function () {
            body.classList.add("modalVisible")
            modalContent.innerHTML = '<div class="mail">' + trigger.dataset.modalContenu + '</div>'
        })
    })
}

// show form address
$('div[name=addAddress]').on('click', function (e) {
    e.preventDefault()
    $(this).addClass('d-none')
    $(this).parent().find('.formValueGroup').removeClass('hideForm')
    $(this).parent().find('button[name=enableBtn]').removeClass('d-none')
    $(this).parent().find('button[name=updateForm]').addClass('d-none')
    let formIdName = $(this).attr('data-formId')
    $('#' + formIdName).find('input').removeAttr('disabled').addClass('modifying')
    $('#' + formIdName).find('select').removeAttr('disabled').addClass('modifying')

})


//show indicator div if a number has been typed
if ($('.phoneInput').val()) {
    $(this).parent().find('.indicator').removeClass("d-none")
} else {
    $(this).parent().find('.indicator').addClass("d-none")
}
$('.phoneInput').on('click', function () {
    // if($(this).val()){
    $(this).parent().find('.indicator').removeClass("d-none")
    // }
})

//change indicator html if country is given
$('select.pays').on('change', function (e) {
    // alert( this.value )
    let indicator = $("option:selected", this)[0].dataset.indicator
    let divToChange = $(this).parent().parent().parent().find(" .indicator")
    if (indicator) {
        divToChange.html("+" + indicator)
        divToChange.removeClass('d-none')
    } else {
        divToChange.html("")
        divToChange.addClass('d-none')
    }

});
//input file change image
$('input[name=logo]').on('change', function (e) {
    $(this).parent().find('img').attr("src", $('#app_url').html() + "storage/app/public/FPF-default-image.jpg")
})

//show select on click
$("button[name=showSelect]").on('click', function (e) {
    e.preventDefault()
    $(this).parent().find('select').removeClass('hidden')
})


// -- CODE From FRONTEND_SC.JS --

$('.modalEditClose').on('click', function (e) {
    e.preventDefault()
    $(this).parent().parent().addClass('d-none')
})
$('.modalEditCloseReload').on('click', function (e) {
    e.preventDefault()
    $(location).attr('href', $(location).attr('href'))
})

$('#dropdownLink').on('click', function (e) {
    e.preventDefault()
    if ($(this).parent().hasClass('active')) {
        $(this).parent().removeClass('active')
    } else {
        $(this).parent().addClass('active')
    }
})

$('#dropdownHeader').on('click', function (e) {
    e.preventDefault()
    if ($(this).hasClass('active')) {
        $(this).removeClass('active')
    } else {
        $(this).addClass('active')
    }
})
$('a[name=changeCardUser]').on('click', function (e) {
    const ref = $(this).data('ref')
    $.ajax({
        url: '/api/personnes/updateSession',
        type: 'POST',
        data: {
            ref: ref
        },
        success: function (data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (err) {
            alert("Erreur lors de la prise en compte du changement de carte")
        }
    })
})

$('a[name=btnReadhesion]').on('click', function (e) {
    const ref = $(this).data('ref')
    const newurl = $(this).data('url')
    $.ajax({
        url: '/api/personnes/updateSession',
        type: 'POST',
        data: {
            ref: ref
        },
        success: function (data) {
            $(location).attr('href', newurl)
        },
        error: function (err) {
            alert("Erreur lors de la prise en compte du changement de carte")
        }
    })
})

$('a[name=linkDropdownHeader]').on('click', function (e) {
    e.stopPropagation()
})

$('#connectConcours').on('click', function (e) {
    e.preventDefault()
    $.ajax({
        url: '/api/personnes/getSession',
        success: function (data) {
            const email = data.email
            const password = data.password
            const cartes = data.cartes
            // on appel en POST l'autoload de l'outil concours
            let form = '';
            form += '<input type="hidden" name="email" value="' + email + '">';
            form += '<input type="hidden" name="password" value="' + password + '">';
            form += '<input type="hidden" name="cartes" value="' + cartes + '">';
            $('<form action="' + $('#app_url_copain').html() + 'webroot/utilisateurs/autoload" method="POST">' + form + '</form>').appendTo($(document.body)).submit();
        },
        error: function (err) {
            alert("Erreur lors de la redirection vers l'outil concours")
        }
    })
})

$('#connectNewsletter').on('click', function (e) {
    e.preventDefault()
    $.ajax({
        url: '/api/personnes/setCookiesForNewsletter',
        success: function (data) {
            if (data.droit_news == 1) {
                $(location).attr('href', 'https://newsletters.federation-photo.fr/autologin')
            } else {
                alert("Vous n'avez pas les droits suffisants pour accéder à la gestion de la newsletter")
            }
        },
        error: function (err) {
            alert("Erreur lors de la redirection vers l'outil newsletter")
        }
    })
})

$('select[name=selectAffectationUr]').on('change', function (e) {
    const ur = $(this).val()
    $(this).parent().parent().find('button[name=validAffectationUr]').data('ur', ur)
})

$('button[name=validAffectationUr]').on('click', function (e) {
    const ur = $(this).data('ur')
    const identifiant = $(this).data('identifiant')
    $('#urAffectation').html(ur)
    $('#confirmAffectationUr').data('ur', ur)
    $('#confirmAffectationUr').data('identifiant', identifiant)
    $('#modalAffectation').removeClass('d-none')
})
$('#confirmAffectationUr').on('click', function (e) {
    const ur = $(this).data('ur')
    const identifiant = $(this).data('identifiant')
    $.ajax({
        url: '/api/personnes/affectationUr',
        type: 'POST',
        data: {
            ur: ur,
            identifiant: identifiant,
        },
        success: function (data) {
            $(location).attr('href', $(location).attr('href'))
        },
        error: function (err) {
            alert("Erreur lors de l'affectation de l'adhérent à l'UR")
        }
    })
})







