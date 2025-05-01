let video=document.querySelector("video")

function main(){
    let rect=video.getBoundingClientRect()
    let windowHeight=innerHeight||document.documentElement.clientHeight
    let visibleArea=Math.max(0,Math.min(rect.bottom,windowHeight)-Math.max(rect.top,0))

    if(0.5<=visibleArea/rect.height){
        video.play()
    }else{
        video.pause()
    }
}

window.onresize=function(){
	main()
}

document.onscroll=function(){
	main()
}