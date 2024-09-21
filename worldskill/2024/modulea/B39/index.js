let count=0

document.getElementById("newcounter").onclick=function(){
	document.getElementById("main").innerHTML=`
		${document.getElementById("main").innerHTML}
		<div class="counter">
			<div class="countertext" id="countertext_${count}">0</div>
			<div class="textcenter">
				<input type="button" class="counterbutton decrease" id="decrease_${count}" value="decrease">
				<input type="button" class="counterbutton increase" id="increase_${count}" value="increase">
			</div>
		</div>
	`

	count=count+1

	document.querySelectorAll(".decrease").forEach(function(event){
		event.onclick=function(){
			document.getElementById("countertext_"+event.id.split("_")[1]).innerHTML=parseInt(document.getElementById("countertext_"+event.id.split("_")[1]).innerHTML)-1
		}
	})

	document.querySelectorAll(".increase").forEach(function(event){
		event.onclick=function(){
			document.getElementById("countertext_"+event.id.split("_")[1]).innerHTML=parseInt(document.getElementById("countertext_"+event.id.split("_")[1]).innerHTML)+1
		}
	})
}