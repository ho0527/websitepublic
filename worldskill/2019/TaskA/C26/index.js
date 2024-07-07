// Get the page dimensions
let windowwwidth=window.innerWidth-40 // Subtract padding
let windowwheight=window.innerHeight-40 // Subtract padding

document.querySelectorAll(".circle").forEach(function(event){
    let size=Math.floor(Math.random()*(400-300+1))+300

    event.style.width=size+"px"
    event.style.height=size+"px"
    event.style.top=Math.floor(Math.random()*(windowwheight-size))+"px"
    event.style.left=Math.floor(Math.random()*(windowwwidth-size))+"px"
})