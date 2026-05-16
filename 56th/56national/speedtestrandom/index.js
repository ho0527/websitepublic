let questionlist=[
	"A01","A02","A03","A04","A05","A06","A07","A08","A09","A10","A11","A12","A13","A14","A15","A16","A17","A18","A19",
	"B01","B02","B03","B04","B05","B06","B07","B08","B09","B10","B11","B12","B13","B14","B15","B16","B17","B18","B19","B20","B21","B22","B23","B24","B25","B26",
	"C01","C02","C03","C04","C05","C06","C07","C08","C09","C10","C11","C12",
	"D01","D02","D03","D04","D05"
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
