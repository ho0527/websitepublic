const WEBLSNAME="worldskill2024modulee-"

let maindata={
	"type": "manual", // manual auto random
	"theme": "A" // A~F
}

if(weblsget(WEBLSNAME+"maindata")){
	maindata=json(weblsget(WEBLSNAME+"maindata"))
}

onclick("#operating",function(element,event){
	innerhtml("#main",`
		<div class="selection">
			<label class="item">
				<input type="radio" name="type" value="manual">
				<div class="text">manual</div>
			</label>
			<label class="item">
				<input type="radio" name="type" value="auto">
				<div class="text">auto</div>
			</label>
			<label class="item">
				<input type="radio" name="type" value="random">
				<div class="text">random</div>
			</label>
		</div>
	`,false)

	domgetall("input[name='type'][value='"+maindata["type"]+"']")[0].checked=true

	onclick("input[name='type']",function(element,event){
		maindata["type"]=element.value
		weblsset(WEBLSNAME+"maindata",maindata)
		innerhtml("#main",``,false)
	})
})

onclick("#theme",function(element,event){
	innerhtml("#main",`
		<div class="selection">
			<label class="item">
				<input type="radio" name="theme" value="A">
				<div class="text">theme A</div>
			</label>
			<label class="item">
				<input type="radio" name="theme" value="B">
				<div class="text">theme B</div>
			</label>
			<label class="item">
				<input type="radio" name="theme" value="C">
				<div class="text">theme C</div>
			</label>
			<label class="item">
				<input type="radio" name="theme" value="D">
				<div class="text">theme D</div>
			</label>
			<label class="item">
				<input type="radio" name="theme" value="E">
				<div class="text">theme E</div>
			</label>
			<label class="item">
				<input type="radio" name="theme" value="F">
				<div class="text">theme F</div>
			</label>
		</div>
	`,false)

	domgetall("input[name='theme'][value='"+maindata["theme"]+"']")[0].checked=true

	onclick("input[name='theme']",function(element,event){
		maindata["theme"]=element.value
		weblsset(WEBLSNAME+"maindata",maindata)
		innerhtml("#main",``,false)
	})
})

onclick("#photo",function(element,event){
	innerhtml("#main",`
		<div class="selection">
			<label class="item">
				<input type="radio" name="type" value="manual">
				<div class="text">manual</div>
			</label>
			<label class="item">
				<input type="radio" name="type" value="auto">
				<div class="text">auto</div>
			</label>
			<label class="item">
				<input type="radio" name="type" value="random">
				<div class="text">random</div>
			</label>
		</div>
	`,false)

	console.log(maindata)
	domgetall("input[name='type'][value='"+maindata["type"]+"']")[0].checked=true

	onclick("input[name='type']",function(element,event){
		maindata["type"]=element.value
		weblsset(WEBLSNAME+"maindata",maindata)
		innerhtml("#main",``,false)
	})
})

// domgetid("mode").onclick=function(){
//     if(!document.fullscreenElement){
//         document.documentElement.requestFullscreen()
//         domgetid("main").classList.remove("indexmain")
//         domgetid("main").classList.remove("macossectiondivy")
//         domgetid("main").classList.add("presentationmain")
//         domgetid("main").classList.add("center")
//         presentationmain(0)
//         this.value="design mode"
//     }else{
//         document.exitFullscreen()
//         domgetid("main").classList.add("indexmain")
//         domgetid("main").classList.add("macossectiondivy")
//         domgetid("main").classList.remove("presentationmain")
//         domgetid("main").classList.remove("center")
//         main()
//         this.value="presentation mode"
//     }
// }