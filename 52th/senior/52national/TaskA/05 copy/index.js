let file=document.getElementById("file")
let cropdownloadbutton=document.getElementById("cropdownloadbutton")
let imagediv=document.getElementById("imagediv")
let image
let filename
let drawing=false
let cropdiv=document.getElementById("cropdiv")

file.addEventListener("change",function(event){
	filename=event.target.files[0].name
})

// 當按下提交按鈕後，禁用提交按鈕並啟用重整按鈕
document.getElementById("submit").addEventListener("click",function(){
	let reader=new FileReader()
	reader.onload=function(){
		image=new Image()
		image.src=reader.result
		image.onload=function(){
			document.getElementById("image").src=image.src
		}
	}
	reader.readAsDataURL(file.files[0])
	cropdownloadbutton.disabled=false
	setTimeout(function(){
		let img=document.getElementById("image")
		let x
		let y
		cropdiv.style.display="block"
		img.addEventListener("pointerdown",function(event){
			if(drawing==true){
				drawing=false
			}else{
				x=event.pageX
				y=event.pageY
				cropdiv.style.left=x+"px"
				cropdiv.style.top=y+"px"
				drawing=true
			}
		})

		img.addEventListener("pointermove",function(event){
			if(drawing==true){
				let x2=event.pageX
				let y2=event.pageY
				cropdiv.style.width=x2-x-5+"px"
				cropdiv.style.height=y2-y-5+"px"
			}
		})

		img.addEventListener("pointerup",function(){
			if(drawing==true){
				drawing=false
			}
		})
	},1000)
})

let iscrop
cropdownloadbutton.onclick=function(){
	if(cropdownloadbutton.value=="crop"){
		let canvas=document.createElement("canvas")
		let image2=document.getElementById("image")
		canvas.width=imagediv.offsetWidth
		canvas.height=imagediv.offsetHeight
		// canvas.width=image2.naturalWidth
		// canvas.height=image2.naturalHeight
		let position=cropdiv.getBoundingClientRect()
		console.log(position.top)
		console.log(position.bottom)
		console.log(position.left)
		console.log(position.right)
		// canvas.getContext("2d").drawImage(document.getElementById("image"),pos.left,pos.top,cropdiv.offsetWidth,cropdiv.offsetHeight,0,0,cropdiv.offsetWidth,cropdiv.offsetHeight)
		canvas.getContext("2d").drawImage(document.getElementById("image"),position.left,position.top,position.right-position.left,position.bottom-position.top,position.left,position.top,position.right-position.left,position.bottom-position.top)
		// canvas.getContext("2d").drawImage(document.getElementyId("image"),position.left,position.top,cropdiv.offsetWidth,cropdiv.offsetHeight,0,0,cropdiv.offsetWidth,cropdiv.offsetHeight)
		let a=document.createElement("a")
		a.href=canvas.toDataURL()
		a.download="crop_"+filename
		iscrop=a
		cropdownloadbutton.value="download"
	}else{
		iscrop.click()
	}
}

// 當按下重整按鈕後，重新載入頁面
document.getElementById("reflashbutton").onclick=function(){
	location.reload()
}