function main(){
	let video=document.getElementById("video")
    let rect=video.getBoundingClientRect()

    if(0.5<=((Math.min(rect.bottom,innerHeight)-Math.max(rect.top,0)))/(rect.height)){
        video.play()
    }else{
        video.pause()
    }
}

document.getElementById("calltoactionbutton").onmousemove=function(event){
	let rect=this.getBoundingClientRect();
	let x=(event.clientX - rect.left) / rect.width * 100;
	let y=(event.clientY - rect.top) / rect.height * 100;
	this.style.setProperty("--x",`${x}%`)
	this.style.setProperty("--y",`${y}%`)
}

document.getElementById("calltoactionbutton").onmouseleave=function(event){
	this.style.setProperty("--x",`300%`)
	this.style.setProperty("--y",`300%`)
}

document.getElementById("read").onclick=function(){
    let text="Mairie de Lyon,69205 Lyon cedex 01"
    let utter=new SpeechSynthesisUtterance(text)
    utter.lang="en-US"
    speechSynthesis.speak(utter)
}

window.onresize=function(){
	main()
}

document.onscroll=function(){
	main()
}