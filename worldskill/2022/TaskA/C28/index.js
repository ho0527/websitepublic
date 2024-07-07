let color=false
let tablemain=``

for(let i=0;i<16;i=i+1){
    tablemain=`
        ${tablemain}
        <tr class="tr">
    `
    for(let j=0;j<16;j=j+1){
        tablemain=`
            ${tablemain}
            <td class="td"></td>
        `
    }
    tablemain=`
        ${tablemain}
        </tr>
    `
}

document.getElementById("table").innerHTML=tablemain

document.querySelectorAll(".td").forEach(function(event){
    event.onclick=function(){
        if(color!=false){
            this.style.backgroundColor=color
        }
    }
})

document.querySelectorAll(".color").forEach(function(event){
    event.style.borderColor="black"
    event.style.width=getComputedStyle(event).getPropertyValue("height")
    event.style.backgroundColor=event.id

    event.onclick=function(){
        if(this.style.borderColor=="black"){
            document.querySelectorAll(".color").forEach(function(event2){
                event2.style.borderColor="black"
            })
            this.style.borderColor="yellow"
            color=this.id
        }else{
            this.style.borderColor="black"
            color=false
        }
    }
})