document.getElementById("submit").onclick=function(){
    let input=document.getElementById("input").value
    let output=document.getElementById("output")

    if(/^(M|CM|D|CD|C|XC|L|XL|X|IX|V|IV|I)+$/.test(input)){
        let result=0
        let map={
            "I":1,
            "V":5,
            "X":10,
            "L":50,
            "C":100,
            "D":500,
            "M":1000
        }

        for(let i=0;i<input.length;i=i+1){
            if(map[input.charAt(i+1)]&&map[input.charAt(i)]<map[input.charAt(i+1)]){
                result=result+map[input.charAt(i+1)]-map[input.charAt(i)]
                i=i+1
            }else{
                result=result+map[input.charAt(i)]
            }
        }
        output.innerHTML="output:"+result;
    }else if(/^[0-9]+$/.test(input)){
        let intdata=parseInt(input)
        let result=""
        let map=[
            { value:1000,numeral:"M" },
            { value:900,numeral:"CM" },
            { value:500,numeral:"D" },
            { value:400,numeral:"CD" },
            { value:100,numeral:"C" },
            { value:90,numeral:"XC" },
            { value:50,numeral:"L" },
            { value:40,numeral:"XL" },
            { value:10,numeral:"X" },
            { value:9,numeral:"IX" },
            { value:5,numeral:"V" },
            { value:4,numeral:"IV" },
            { value:1,numeral:"I" }
        ]
        for(let i=0;i<map.length;i=i+1){
            while(intdata>=map[i].value){
                result=result+map[i].numeral
                intdata=intdata-map[i].value
            }
        }
        output.innerHTML="output:"+result
    }else{
        output.innerHTML="[ERROR] Invalid input!"
    }
}