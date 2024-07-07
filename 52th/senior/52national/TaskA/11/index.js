function removearrayduplicate(array){
    let uniquearray=[];
    let seenelement={};
    for(let i=0;i<array.length;i=i+1){
        let element=array[i];
        if(!seenelement[element]){
            uniquearray.push(element);
            seenelement[element]=true;
        }
    }
    return uniquearray;
}

document.getElementById("submit").onclick=function(){
    let csvdata=[]
    let county=[]
    let county1=[]
    let county2=[]
    let total=[]
    let countytotal=[]
    let largestnumber
    let reader=new FileReader()
    reader.onload=function(event){
        const lines=event.target.result.split("\n")
        let ans=""
        for(let i=0;i<lines.length;i=i+1){
            let line=lines[i].split(",")
            csvdata.push(line)
            ans=ans+"<br>"+line
        }
        let countytotaltemp=[]
        for(let i=1;i<lines.length;i=i+1){
            if(csvdata[i][0]!=""){
                county1.push(csvdata[i][0])
                county2.push(csvdata[i][1])
                let totalsum=parseInt(csvdata[i][2])+parseInt(csvdata[i][3])
                total.push(totalsum)
                county.push([csvdata[i][0]+" "+csvdata[i][1],totalsum])
                countytotaltemp.push([csvdata[i][0],totalsum])
            }
        }
        county1=removearrayduplicate(county1)
        for(let i=0;i<countytotaltemp.length;i=i+1){
            let index=countytotal.findIndex(function(c){ return c[0]==countytotaltemp[i][0] })
            if(index==-1){
                countytotal.push([countytotaltemp[i][0],1])
            }else{
                countytotal[index][1]=countytotal[index][1]+countytotaltemp[i][1]
            }
        }
        // document.getElementById("log").innerHTML=ans
    }
    reader.readAsText(document.getElementById("inputfile").files[0])
    setTimeout(function(){
        let temptotal=countytotal
        temptotal.sort(function(a,b){
            return b-a
        })
        largestnumber=temptotal[0]
        let x=Math.ceil(largestnumber[1]/1000)*1000
        document.getElementById("table").innerHTML=``
        for(i=0;i<county1.length;i=i+1){
            let tr=document.createElement("tr")
            tr.classList.add("tr")
            document.getElementById("table").appendChild(tr)
            let td=document.createElement("td")
            td.classList.add("td")
            td.classList.add("tdtitle")
            td.id=i
            td.innerHTML=`${county1[i]}`
            document.querySelectorAll(".tr")[i].appendChild(td)
            let td2=document.createElement("td")
            td2.classList.add("td")
            td2.classList.add("tdshow")
            td2.id=i+"2"
            td2.innerHTML=`
                <div class="line"></div>
            `
            document.querySelectorAll(".tr")[i].appendChild(td2)
            let td3=document.createElement("td")
            td3.classList.add("td")
            td3.classList.add("tdnumber")
            td3.id=i+"3"
            td3.innerHTML=`${countytotal[i][1]}`
            document.querySelectorAll(".tr")[i].appendChild(td3)
        }
        setTimeout(function(){
            for(i=0;i<county1.length;i=i+1){
                document.querySelectorAll(".line")[i].style.width=(countytotal[i][1]/x)*100+"%"
            }
        },100)
        document.querySelectorAll(".tdshow").forEach(function(event){
            event.onclick=function(){
                for(let i=0;i<county.length;i=i+1){
                    console.log("county.length="+county.length)
                    console.log(i+"="+county[i])
                }
            }
        })
    },100)
}

document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}