let navbarMenu = document.querySelector('.navbar .menu')
let navbar = document.querySelector(".navbar")
let hamburger = document.querySelector('.hamburgerIcon')


const navbarManagement = function () {
    navbar.classList.toggle('hidden')
    hamburger.classList.toggle('close')
}
if (window.innerWidth < 992) {
    hamburger.addEventListener("click", navbarManagement)
}
window.addEventListener("resize", () => {
    if (window.innerWidth < 992) {
        navbar.classList.add('hidden')
        hamburger.classList.remove('close')
        hamburger.addEventListener("click", navbarManagement)
    }else{
        navbar.classList.remove('hidden')
        hamburger.removeEventListener("click", navbarManagement)
    }
})
//vanilla tilt animation
// animatedCards = document.querySelectorAll(".cardContainer .card")
// if (animatedCards.length) {
//     animatedCards.forEach((item) => {
//         VanillaTilt.init(item, {
//             max: 25,
//             speed: 400
//         });
//     })
// }


