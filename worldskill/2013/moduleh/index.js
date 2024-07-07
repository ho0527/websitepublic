let sidercount=0
let count=0

function showall(id){
    if(document.getElementById(id)){
        document.getElementById(id).style.display="block"
        document.getElementById(id).style.height="0px"
        document.getElementById(id).style.transition="2s linear"
        setTimeout(function(){
            document.getElementById(id).style.height="100%"
        },20)
    }else{
        conlog("[ERROR]function showall id not found","red","12")
    }
}

setInterval(function(){
    sidercount=sidercount+1
    if(sidercount==150){
        sidercount=0
        count=(count+1)%3
        document.getElementById("sliderimage").src="material/Layout/i/Slide"+(count+1)+".jpg"
    }
},10)

document.getElementById("prev").onclick=function(){
    sidercount=0
    count=(count-1)%3
    if(count<0){
        count=2
    }
    document.getElementById("sliderimage").src="material/Layout/i/Slide"+(count+1)+".jpg"
}

document.getElementById("next").onclick=function(){
    sidercount=0
    count=(count+1)%3
    document.getElementById("sliderimage").src="material/Layout/i/Slide"+(count+1)+".jpg"
}