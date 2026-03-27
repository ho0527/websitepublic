let isDragging=false

function startDrag(e){isDragging=true}

function stopDrag(){isDragging=false}

function moveDrag(e){
	if(!isDragging)return
	let container=document.querySelector("div")
	let beforeImg=document.querySelector("img:first-of-type")
	let rect=container.getBoundingClientRect()
	let pos=Math.min(Math.max(0,e.clientX-rect.left),rect.width)
	beforeImg.style.clipPath=`inset(0 ${100-pos/rect.width*100}% 0 0)`
}