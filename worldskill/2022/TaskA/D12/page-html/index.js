document.querySelectorAll(".video").forEach(function(event){
	event.onmouseover=function(){
		event.pause()
	}
	event.onmouseleave=function(){
		event.play()
	}
})