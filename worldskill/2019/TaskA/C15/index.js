let main=document.getElementById("main")

main.onpointermove=function(event){
    let x=(event.clientX/main.offsetWidth)-0.5;
    let y=(event.clientY/main.offsetHeight)-0.5;

    document.querySelectorAll(".layer").forEach(function(event){
        event.style.transform="translate("+(x*(parseInt(event.id)+1)*30)+"px,"+(y*(parseInt(event.id)+1)*30)+"px)";
    })
}