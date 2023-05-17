console.log("load script")

let navbarMenu = document.querySelector('.navbar .menu')
let navbar = document.querySelector(".navbar")
let hamburger = document.querySelector('.hamburger')

if (navbarMenu) {
    let items = navbarMenu.querySelectorAll('.menuItem')
    let url = window.location.origin
    items.forEach((item) => {
        let link = item.dataset.direction
        item.addEventListener("click", function () {
            window.location = url + "/" + link
        })
    })
}
const navbarManagement = function () {
    navbar.classList.toggle('hidden')
    hamburger.classList.toggle('close')
}
if (window.innerWidth < 1201) {
    hamburger.addEventListener("click", navbarManagement)
}
window.addEventListener("resize", () =>{
    if (window.innerWidth < 1201) {
        console.log("hide navbar")
        navbar.classList.add('hidden')
        hamburger.addEventListener("click", navbarManagement)
    }else{
        navbar.classList.remove('hidden')
        hamburger.removeEventListener("click", navbarManagement)
    }
})
//vanilla tilt animation
animatedCards = document.querySelectorAll(".cardContainer .card")
if (animatedCards.length) {
    animatedCards.forEach((item) => {
        VanillaTilt.init(item, {
            max: 25,
            speed: 400
        });
    })
}


