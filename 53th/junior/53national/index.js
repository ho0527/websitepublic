let ajax=new XMLHttpRequest()

ajax.onreadystatechange=function(){
    if(ajax.readyState==4){
        if(ajax.status==200){
            let data=JSON.parse(ajax.responseText)
            let maindiv=document.getElementById("maindiv")
            console.log(data)
            let div=document.createElement("div")
            div.id=0
            div.classList.add("div")
            div.classList.add("divleft")
            maindiv.appendChild(div)
            document.getElementById(0).innerHTML=`
                <div class="circle">
                    <div class="outcircle">
                        <div class="incircle"></div>
                    </div>
                </div>
                <div class="site">${data[1][0][1]}</div>
            `
            let kx=200
            let y=0  //y=50px
            for(let i=1;i<data[1].length;i=i+1){
                let site=data[1][i][1]
                let div=document.createElement("div")
                div.id=i
                div.classList.add("div")
                let line=document.createElement("div")
                line.id="line"+i
                line.classList.add("line")
                if(i%3==1){
                    div.classList.add("divmid")
                    line.classList.add("linemidleft")
                }else if(i%3==2){
                    if(i%2==0){
                        div.classList.add("divright")
                        line.classList.add("linemidright")
                    }else{
                        div.classList.add("divleft")
                        line.classList.add("linemidleft")
                    }
                }else{
                    if(i%2==0){
                        div.classList.add("divleft")
                        line.classList.add("lineleft")
                    }else{
                        div.classList.add("divright")
                        line.classList.add("lineright")
                    }
                    y=y+1
                }
                maindiv.appendChild(div)
                maindiv.appendChild(line)
                document.getElementById("line"+i).style.top=(95+(y*50))+"px"
                document.getElementById(i).innerHTML=`
                    <div class="circle">
                        <div class="outcircle">
                            <div class="incircle"></div>
                        </div>
                    </div>
                    <div class="site">${site}</div>
                `
            }
            for(let i=0;i<data[0].length;i=i+1){
                console.log(data[0][i])
            }
        }else{
            console.log("[ERROR] ajax error")
        }
    }
}

ajax.open("GET","apiindex.php",true)
ajax.send()