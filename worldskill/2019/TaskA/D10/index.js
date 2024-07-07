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
		let drawing=false
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

let alink
cropdownloadbutton.onclick=function(){
	if(cropdownloadbutton.value=="crop"){
		let canvas=document.createElement("canvas")
		cropdiv=document.getElementById("cropdiv")
		canvas.width=cropdiv.offsetWidth
		canvas.height=cropdiv.offsetHeight
		let position=cropdiv.getBoundingClientRect()
		canvas.getContext("2d").drawImage(document.getElementById("image"),position.left-5,position.top-5,canvas.width,canvas.height,0,0,canvas.width,canvas.height)
		let a=document.createElement("a")
		a.href=canvas.toDataURL("image/png",1)
		a.download="crop_"+filename
		alink=a
		cropdownloadbutton.value="download"
	}else{
		alink.click()
	}
}

// 當按下重整按鈕後，重新載入頁面
document.getElementById("reflashbutton").onclick=function(){
	location.reload()
}