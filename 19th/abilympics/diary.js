//長心得展開 / 收合
document.querySelectorAll(".cutbutton").forEach(function(button){
	button.onclick=function(){
		let box=this.parentElement

		if(box.classList.contains("cutopen")){
			box.classList.remove("cutopen")
			this.value="閱讀更多"
		}else{
			box.classList.add("cutopen")
			this.value="收合"
		}
	}
})

//極光祝福
document.querySelectorAll(".bless").forEach(function(button){
	button.onclick=async function(){
		let id=this.dataset.id
		let ajax=await fetch("api.php?key=bless&id="+id)
		let data=await ajax.json()

		if(data["success"]){
			document.getElementById("blesscount_"+id).innerText=data["data"]["blesscount"]
		}else{
			alert("送出祝福失敗，請稍後再試")
		}
	}
})

//投稿表單驗證
function showerror(id,message){
	document.getElementById("error_"+id).innerText=message
}

document.getElementById("diaryform").onsubmit=function(event){
	let name=document.getElementById("name").value.trim()
	let email=document.getElementById("email").value.trim()
	let location=document.getElementById("location").value
	let date=document.getElementById("date").value
	let rating=document.getElementById("rating").value
	let content=document.getElementById("content").value.trim()
	let photo=document.getElementById("photo").value.trim()
	let pass=true

	showerror("name","")
	showerror("email","")
	showerror("location","")
	showerror("date","")
	showerror("rating","")
	showerror("content","")
	showerror("photo","")

	if(name==""){
		showerror("name","請填寫旅人暱稱，此欄位不得留空")
		pass=false
	}

	if(email==""){
		showerror("email","請填寫 Email，此欄位不得留空")
		pass=false
	}else if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)==false){
		showerror("email","Email 格式不正確，請輸入如 name@example.com")
		pass=false
	}

	if(location==""){
		showerror("location","請選擇觀賞地點")
		pass=false
	}

	if(date==""){
		showerror("date","請選擇觀賞日期")
		pass=false
	}

	if(rating==""){
		showerror("rating","請選擇極光評分")
		pass=false
	}else if(parseInt(rating)<1||parseInt(rating)>5){
		showerror("rating","極光評分僅能填 1 至 5 之間的整數")
		pass=false
	}

	if(content==""){
		showerror("content","請填寫觀賞心得，此欄位不得留空")
		pass=false
	}else if(content.length<10){
		showerror("content","觀賞心得至少需要 10 個字，目前只有 "+content.length+" 個字")
		pass=false
	}

	if(photo==""){
		showerror("photo","請填寫照片網址或檔名，此欄位不得留空")
		pass=false
	}

	if(pass==false){
		event.preventDefault()
	}
}
