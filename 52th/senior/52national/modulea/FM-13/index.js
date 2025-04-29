document.querySelectorAll("#button").forEach(function(button){
    button.addEventListener("click",function(e){
        var offset=this.getBoundingClientRect()
        var x=e.pageX
        var y=e.pageY
        var ripple=document.createElement("span")
        ripple.classList.add("ripple")
        this.appendChild(ripple)
        ripple.style.left=x-offset.left-ripple.offsetWidth/2+"px"
        ripple.style.top=y-offset.top-ripple.offsetHeight/2+"px"
    })
})