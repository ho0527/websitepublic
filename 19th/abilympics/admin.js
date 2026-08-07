if(localStorage.getItem(WEBLSNAME+"signin")!="true"){
	alert("請先登入")
	location.href="signin.php"
}

//長心得展開 / 收合
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

//刪除日記
document.querySelectorAll(".delete").forEach(function(button){
	button.onclick=function(){
		if(confirm("確定要刪除「"+this.dataset.name+"」的這篇日記嗎？")){
			location.href="api.php?key=deletediary&id="+this.dataset.id
		}
	}
})
