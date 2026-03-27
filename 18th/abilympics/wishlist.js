let interval=""

document.querySelectorAll(".like").forEach(function(button){
	button.onclick=async function(){
		let id=this.dataset.id
		let content=prompt("請輸入祝福語(上限50個字元)")

		if(content.length<50){
			let ajax=await fetch("api.php?key=like&id="+id+"&content="+content)
			let data=await ajax.json()

			if(data["success"]){
				alert("已送出愛心")
				document.getElementById("likecount_"+id).innerText=parseInt(document.getElementById("likecount_"+id).innerText)+1
			}else{
				alert("送出愛心失敗，請稍後再試")
			}
		}else{
			alert("請勿輸入大於50個字元")
		}
	}
})

document.querySelectorAll(".likehover").forEach(function(button){
	button.onmouseover=function(){
		let id=this.dataset.id

		function main(){
			let contentlist=JSON.parse(document.getElementById("wish_"+id).dataset.content)
			let content=contentlist[Math.floor(Math.random()*contentlist.length)]

			document.getElementById("likecontent_"+id).style.display="block"
			document.getElementById("likecontent_"+id).innerHTML=`${content["content"]}`
		}

		interval=setInterval(function(){
			main()
		},1000)
		main()
	}
	button.onmouseleave=function(){
		let id=this.dataset.id
		document.getElementById("likecontent_"+id).style.display="none"
		clearInterval(interval)
	}
})