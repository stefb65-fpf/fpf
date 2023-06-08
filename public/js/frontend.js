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
                        isSearching = true
                    } else {
                        isSearching = false
                    }
                } else {
                    searchItem.classList.remove("active")
                }
            }
        })
    if(isSearching){
        searchBox.classList.add("searching")
    }else{
        searchBox.classList.remove("searching")
    }
    })

}

