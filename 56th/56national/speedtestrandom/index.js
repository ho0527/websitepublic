let questionlist=[
	"19A01","19A02","19A03","19A04","19A05","19A06","19A07",
	"19B01","19B02","19B03","19B04","19B05","19B06","19B07","19B08","19B09","19B010","19B11","19B12","19B13","19B14","19B15",
	"19C01","19C02","19C03","19C04","19C05","19C06","19C07","19C08","19C09","19C10","19C11","19C12","19C13","19C14","19C15","19C16","19C17","19C19","19C24","19C25","19C26","19C27",
	"19D01","19D02","19D03","19D04","19D05","19D06","19D08","19D10","19D11","19D12","19D13",
	"22A01","22A02","22A03","22A04","22A05","22A06","22A07","22A08","22A09","22A10","22A11",
	"22B01","22B02","22B03","22B04","22B05","22B06","22B07","22B08","22B09","22B10","22B11","22B12","22B13","22B14","22B15","22B16","22B17",
	"22C01","22C02","22C03","22C04","22C05","22C06","22C07","22C08","22C09","22C10","22C11","22C12","22C13","22C14","22C15","22C16","22C17","22C18","22C19","22C20","22C21","22C22","22C23","22C24","22C25","22C26","22C27","22C28","22C29",
	"22D01","22D02","22D03","22D04","22D05","22D06","22D07","22D08","22D09","22D10","22D11","22D12","22D13","22D14","22D15","22D16","22D17",
	"24A06","24A13","24A22","24B09","24B35","24B39","24D07","24C01","24C09","24C11",
]
let setinterval

document.getElementById("function").onclick=function(){
	if(this.value=="start"){
		this.value="end"

		setinterval=setInterval(function(){
			let tempquestionlist=[...questionlist]
			function shuffle(array){
				for(let i=array.length-1;i>0;i=i-1){
					let j=Math.floor(Math.random()*(i+1))
					let temp=array[i]
					array[i]=array[j]
					array[j]=temp
				}
				return array
			}
			
			tempquestionlist=shuffle(tempquestionlist)
			tempquestionlist=tempquestionlist.slice(0,10)
			tempquestionlist.sort()
			
			document.getElementById("output").innerHTML=``

			for(let i=0;i<tempquestionlist.length;i=i+1){
				document.getElementById("output").innerHTML=`
					${document.getElementById("output").innerHTML}
					<div>${tempquestionlist[i]}<div>
				`
			}
		},100)
	}else{
		this.value="start"
		clearInterval(setinterval)
	}
}
