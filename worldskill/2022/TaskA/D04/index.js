let count=0

function main(){
    let ajax=new XMLHttpRequest()
    
    ajax.onload=function(){
        let data=JSON.parse(ajax.responseText)
        for(let i=count;i<Math.min(10,data.length-count)+count;i=i+1){
            let div=document.createElement("div")
            div.id=data[i]["id"]
            div.classList.add("grid")
            div.classList.add("datadiv")
            div.innerHTML=`
                <div class="firstname">${data[i]["first_name"]}</div>
                <div class="lastname">${data[i]["last_name"]}</div>
                <div class="email">${data[i]["email"]}</div>
                <div class="country">${data[i]["country"]}</div>
                <div class="city">${data[i]["city"]}</div>
                <div class="phone">${data[i]["phone"]}</div>
            `
            document.getElementById("main").appendChild(div)
        }
        count=count+Math.min(10,data.length-count)
    }
    
    ajax.open("GET","data.json")
    ajax.send()
}

window.onscroll=function(event){
    if((window.innerHeight+Math.round(window.scrollY))>=document.body.offsetHeight){
        main()
    }
}

main()