let page={
    "a.html":"This is page A",
    "b.html":"This is page B",
    "c.html":"This is page C",
}

document.addEventListener("click",function(event){
    event.preventDefault()
    history.pushState(null,null,event.target.href)
    let url=location.href.split("/")
    document.getElementById("content").innerHTML=page[url[url.length-1]]
})

let url=location.href.split("/")
document.getElementById("content").innerHTML=page[url[url.length-1]]