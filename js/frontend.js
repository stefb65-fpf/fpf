let navbarMenu = document.querySelector('.navbar .menu')
let navbar = document.querySelector(".navbar")
let hamburger = document.querySelector('.hamburger')

if(navbarMenu){
    let items = navbarMenu.querySelectorAll('.menuItem')
    let url = window.location.origin
    items.forEach((item)=> {
        let link = item.dataset.direction
       item.addEventListener("click",function(){
           window.location = url +"/"+ link
       })
    })
}

    if(window.innerWidth < 1201){

  hamburger.addEventListener("click",function () {
      navbar.classList.toggle('hidden')
      hamburger.classList.toggle('close')
  })
    }

//vanilla tilt animation
animatedCards = document.querySelectorAll(".cardContainer .card")
if(animatedCards.length ){
    animatedCards.forEach((item)=>{
        VanillaTilt.init(item, {
            max: 25,
            speed: 400
        });
    })
}


