let buttons=document.querySelectorAll('button')
let output=document.getElementById("output")
let a="twodecimals"
//訂定變數

output.value=""//將output清空

document.querySelectorAll(".calckey").forEach(function(event){
	event.addEventListener("click",function(){
		if(this.value=="C"){
			output.value=""//將output清空
		}else if(this.value=="="){
			if(a=="twodecimals"){//判斷a是否為twodecimals
				let result=eval(output.value)
				output.value=Math.round(result*100)/100;//四捨五入
			}else{
				output.value=eval(output.value)//印出結果
			}
		}else{
			if(this.value=="x"){
				output.value=output.value+"*"//加字串
			}else if(this.value=="÷"){
				output.value=output.value+"/"//加字串
			}else{
				output.value=output.value+this.value//加字串
			}
		}
	})
})

output.addEventListener("keydown",function(event){
	if(/[0-9]|\+|\-|\*|\/|\.|Backspace/.test(event.key)){
		console.log(event.key)
	}else{
		alert("不行打奇怪的東西喔喔喔喔")
		setTimeout(function(){
			output.value=output.value.slice(0,-1)
		},200);
	}
})