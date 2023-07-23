let navbar = document.querySelector(".navbar")
let hamburger = document.querySelector('.hamburgerIcon')
let autosuggestContainer = document.querySelector(".autosuggestContainer")
let autosuggestCFA = document.querySelector(".autosuggestCFA")

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

// dropdown menu
let dropdownCalls = document.querySelectorAll('.dropdownCall')
let dropdownParents = document.querySelectorAll('.dropdownParent')
let body = document.querySelector('body')
if (dropdownCalls) {
    body.addEventListener('click', (e) => {
        let isDropDownClicked = false;
        dropdownCalls.forEach((dropdown) => {
            if (e.target == dropdown) {
                isDropDownClicked = true
            }
        })
        if (!isDropDownClicked) {
            dropdownParents.forEach((parent) => {
                parent.classList.remove('active')
            })
        }
    })
    dropdownCalls.forEach((dropdown) => {
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownParents.forEach((parent) => {
                if (parent.dataset.dropdownId === dropdown.dataset.dropdownId) {
                    parent.classList.toggle('active')
                } else {
                    parent.classList.remove('active')
                }
            })
        })
    })
}
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


//autosuggest

if (autosuggestContainer) {
    window.addEventListener('click', (e) => {
        let inputs = autosuggestContainer.querySelectorAll('.autosuggestCFA')
        let outside = true
        inputs.forEach((input) => {
            if (e.target == input) {
                autosuggestContainer.classList.add('active')
                outside = false
            }
        })
        if (outside) {
            autosuggestContainer.classList.remove('active')
        }
    })
}



//account profile
$('button[name=updateForm]').on('click',function(e){
    e.preventDefault()
    let formIdName = $(this).parent().attr('data-formId')
    $(this).addClass('d-none')
    $(this).parent().find('button[name=enableBtn]').removeClass('d-none')
    $('#'+ formIdName).find('.formValue').removeAttr('disabled').addClass('modifying')
    // $(this).parent().parent().find('.formValue').removeAttr('disabled').addClass('modifying')
    $('#'+ formIdName).find('.modifyVisible').removeClass('modifyVisible')

    if($(this).hasClass('showFields')){
        $('#'+ formIdName).find('.hiddenFields').removeClass('hidden')
        // $(this).parent().parent().find('.hiddenFields').removeClass('hidden')
    }
})
//send form on click button[name=enableBtn]
$('button[name=enableBtn]').on('click', function (e) {
    e.preventDefault()
    let formIdName = $(this).parent().attr('data-formId')
    // console.log(   $('#'+ formIdName))
    $('#'+ formIdName).submit()
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

if(checkableBtnOriginal){
    let length = false;
    let lower = false;
    let uppercase = false;
    let number = false;
    let confirmation= false;
        checkableBtnOriginal.addEventListener("keyup", (e) => {
        //verif de la confirmation
        let password = checkableBtnOriginal.value
       if(password === checkableBtnConfirmation.value && checkableBtnOriginal.value.length){
           checkableConfirmation.classList.add("ok")
           confirmation = true
       } else{
           checkableConfirmation.classList.remove("ok")
           confirmation = false
       }
       //verif des regex et longueur
        if(checkableBtnOriginal.value.length > 7 && checkableBtnOriginal.value.length < 36){
            checkableWidth.classList.add("ok")
            length = true
        }else{
            checkableWidth.classList.remove("ok")
            length = false
        }
        const regUpper = /^(.*[A-Z].*)+$/
        if (regUpper.test(password)) {
            checkableupperCaseLetter.classList.add("ok")
            uppercase = true

        }else{
            checkableupperCaseLetter.classList.remove("ok")
            uppercase = false
        }
        const regLower = /^(.*[a-z].*)+$/
        if (regLower.test(password)) {
            checkableSmallLetter.classList.add("ok")
            lower = true
        }else{
            checkableSmallLetter.classList.remove("ok")
            lower=false
        }
        const regNumber = /^(.*[0-9].*)+$/
        if (regNumber.test(password)) {
            checkableNumber.classList.add("ok")
            number=true
        }else{
            checkableNumber.classList.remove("ok")
            number=false
        }
    if(confirmation && number && length && lower && uppercase){
        submitBtn.removeAttribute("disabled")
    }else{
        submitBtn.setAttribute("disabled","")
    }
    });
    checkableBtnConfirmation.addEventListener("keyup", (e) => {
        //verif de la confirmation
        if(checkableBtnOriginal.value === checkableBtnConfirmation.value && checkableBtnConfirmation.value.length){
            checkableConfirmation.classList.add("ok")
            confirmation = true
        } else{
            checkableConfirmation.classList.remove("ok")
            confirmation = false
        }
        if(confirmation && number && length && lower && uppercase){
            submitBtn.removeAttribute("disabled")
        }else{
            submitBtn.setAttribute("disabled","")
        }
    });

}
//modal
let modalBackground = document.querySelector(".modalBackground")
let modalContent = document.querySelector(".modalContent")
let modalClose = document.querySelector(".modalWrapper .close .clickable")
let modalTriggers = document.querySelectorAll(".modalTrigger")

if(modalBackground){
    modalBackground.addEventListener('click', (e)=>{
        e.preventDefault()
        // console.log( e.target == modalBackground?"true":"false",e.target == modalClose?"true":"false")
        if(e.target == modalBackground || e.target == modalClose){
            body.classList.remove("modalVisible")
        }
    })
}
if(modalTriggers.length){
    modalTriggers.forEach((trigger) => {
        trigger.addEventListener('click', function(){
            body.classList.add("modalVisible")
            console.log(trigger.dataset.modalContenu,trigger.dataset.modalStyle)

            // modalContent.innerHTML = "<style>"+trigger.dataset.modalStyle +"</style>"+ trigger.dataset.modalContenu
            modalContent.innerHTML =   '<div class="mail">'+trigger.dataset.modalContenu+'</div>'
        })
    })
}
// show form address
// let addDefaultAddressBtn = document.querySelector(".addDefaultAddress")
// let addLivraisonAddressBtn = document.querySelector(".addFacturationAddress")
$('div[name=addAddress]').on('click',function(e){
    e.preventDefault()
    $(this).addClass('d-none')
    $(this).parent().find('.formValueGroup').removeClass('hideForm')
    $(this).parent().find('button[name=enableBtn]').removeClass('d-none')
    $(this).parent().find('button[name=updateForm]').addClass('d-none')
    let formIdName = $(this).attr('data-formId')
    $('#'+ formIdName).find('input').removeAttr('disabled').addClass('modifying')
    $('#'+ formIdName).find('select').removeAttr('disabled').addClass('modifying')

})


//show indicator div if a number has been typed
if($('.phoneInput').val()){
    $(this).parent().find('.indicator').removeClass("d-none")
}else{
    $(this).parent().find('.indicator').addClass("d-none")
}
$('.phoneInput').on('click', function(){
    // if($(this).val()){
        $(this).parent().find('.indicator').removeClass("d-none")
    // }
})

//change indicator html if country is given
$('select.pays').on('change', function(e) {
    // alert( this.value )
    let indicator = $("option:selected", this)[0].dataset.indicator
    let  divToChange =  $(this).parent().parent().parent().find(" .indicator")
    if(indicator){
        divToChange.html("+"+indicator)
        divToChange.removeClass('d-none')
    }else{
        divToChange.html("")
        divToChange.addClass('d-none')
    }

});
//input file change image
$('input[name=logo]').on('change', function(e){
    console.log($(this).val(),e.currentTarget, e.target.files[0])
    $(this).parent().find('img').attr("src","https://fpf-new.federation-photo.fr/storage/app/public/FPF-default-image.jpg")
    // $(this).attr("value", e.target.files[0])

})

//show select on click
$("button[name=showSelect]").on('click', function(e){
    e.preventDefault()
    console.log("aa")
    $(this).parent().find('select').removeClass('hidden')
})


//searchbox topbar
$('.icon[name=searchBtn]').on("click", function () {
    $(this).parent().toggleClass('active')
    $('.searchContainer').toggleClass('searching')
    $(this).parent().parent().find('input').trigger( "focus" )
})

function searchClub(term) {
    const url = '/api/ajax/getClubs'
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            term: term
        },
        dataType: 'JSON',
        success: function (data) {
            if (data.length > 0) {
            } else {

            }
        },
        error: function (e) {
        }
    });
}
$('.searchBox.club input').on('keypress', function (e) {
    // e.preventDefault()
    if (e.which === 13) {
      searchClub($(this).val());
    }
})
