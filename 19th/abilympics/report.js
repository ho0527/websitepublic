//長備註展開 / 收合
document.querySelectorAll(".cutbutton").forEach(function(button){
	button.onclick=function(){
		let box=this.parentElement

		if(box.classList.contains("cutopen")){
			box.classList.remove("cutopen")
			this.value="展開"
		}else{
			box.classList.add("cutopen")
			this.value="收合"
		}
	}
})
