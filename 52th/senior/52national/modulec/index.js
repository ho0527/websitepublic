let url=location.href.split("#")
let key=url[url.length-1]
if(key=="new"||key=="hot"||key=="map"||key=="connection"){
    document.getElementById("nbar"+key).classList.add("selectbutton")
    document.querySelectorAll(".navigationbarbutton")[0].classList.remove("selectbutton")
}else{
    document.querySelectorAll(".navigationbarbutton")[1].classList.remove("selectbutton")
    document.querySelectorAll(".navigationbarbutton")[2].classList.remove("selectbutton")
    document.querySelectorAll(".navigationbarbutton")[3].classList.remove("selectbutton")
    document.querySelectorAll(".navigationbarbutton")[4].classList.remove("selectbutton")
    document.querySelectorAll(".navigationbarbutton")[0].classList.add("selectbutton")
}

document.querySelectorAll(".navigationbarbutton").forEach(function(event){
    event.onclick=function(){
        if(this.id=="nbarmain"){
            location.href="index.html"
        }else if(this.id=="nbarnew"){
            location.href="#new"
        }else if(this.id=="nbarhot"){
            location.href="#hot"
        }else if(this.id=="nbarmap"){
            location.href="#map"
        }else if(this.id=="nbarconnection"){
            location.href="#connection"
        }else{
            location.href="detail.html"
        }
        setTimeout(function(){  location.reload() },50);
    }
})