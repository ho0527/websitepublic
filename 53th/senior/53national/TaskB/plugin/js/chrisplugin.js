/*
    標題: 插件整合
    參考:
    作者: 小賀chris
    製作及log:
    2023/06/28  20:10:16 Bata 1.0.0 // 新增macosselection 以及 sort 以及 dget 以及 clog 函式
    2023/06/30  14:35:45 Bata 1.0.1 // 新增isset函式
    2023/07/01  12:52:01 Bata 1.0.2 // 修改變數及小問題
    2023/07/01  12:56:24 Bata 1.0.3 // 新增docgetid 及 docgetall 及 weblsset 及 weblsget 函式

        |-------    -----    -                     -     -----  -----  -----   -------|
       |-------    -        -            - - -          -                     -------|
      |-------    -        -------    -          -     -----    --       --  -------|
     |-------    -        -     -    -          -         -      --     --  -------|
    |-------    -----    -     -    -          -     -----         -----  -------|
*/

function startmacos(){
    let macostimer
    setTimeout(function(){
        document.querySelectorAll(".macossectiondiv").forEach(function(event){
            event.addEventListener("scroll",function(){
                clearTimeout(macostimer)
                event.setAttribute("scroll","true")
                macostimer=setTimeout(function(){
                    event.removeAttribute("scroll")
                },500)
            })
        })
        document.querySelectorAll(".macossectiondivx").forEach(function(event){
            event.addEventListener("scroll",function(){
                clearTimeout(macostimer)
                event.setAttribute("scroll","true")
                macostimer=setTimeout(function(){
                    event.removeAttribute("scroll")
                },500)
            })
        })
        document.querySelectorAll(".macossectiondivall").forEach(function(event){
            event.addEventListener("scroll",function(){
                clearTimeout(macostimer)
                event.setAttribute("scroll","true")
                macostimer=setTimeout(function(){
                    event.removeAttribute("scroll")
                },500)
            })
        })
    },200)
}

function divsort(card,sortdiv){ // card 放要被拖的物件 cardclass(不加選擇器) sortdiv 放要放的物件(加選擇器)
    let data=[]

    document.querySelectorAll("."+card).forEach(function(event){
        event.draggable="true"
    })

    document.querySelectorAll(sortdiv).forEach(function(event){
        event.ondragstart=function(addeventlistenerevent){
            addeventlistenerevent.target.classList.add("divsortdragging")
        }

        event.ondragover=function(addeventlistenerevent){
            addeventlistenerevent.preventDefault()
            let sortableContainer=addeventlistenerevent.target.closest(sortdiv)
            if(sortableContainer){
                let draggableElements=Array.from(sortableContainer.children).filter(function(child){
                    return child.classList.contains(card)&&!child.classList.contains("divsortdragging")
                })

                let afterElement=draggableElements.reduce(function(closest,child){
                    let box=child.getBoundingClientRect()
                    let offset=addeventlistenerevent.clientY-box.top-box.height/2
                    if(offset<0&&offset>closest.offset){
                        return { offset:offset,element:child }
                    }else{
                        return closest
                    }
                },{ offset:Number.NEGATIVE_INFINITY }).element

                let draggable=document.querySelector(".divsortdragging")
                if(afterElement==null){
                    sortableContainer.appendChild(draggable)
                }else{
                    sortableContainer.insertBefore(draggable,afterElement)
                }
            }
        }

        event.ondragend=function(addeventlistenerevent){
            addeventlistenerevent.target.classList.remove("divsortdragging")
        }
    })

    document.querySelectorAll(card).forEach(function(event){
        data.push(event)
    })

    return data
}

function docget(key,selector){ // 傳入 id animation class name tag tagns qtor all 分別對應為 getElementById getAnimations getElementsByClassName getElementsByName getElementsByTagName getElementsByTagNameNS querySelector querySelectorAll 之後傳入要執行的selector
    if(key=="id"){
        return document.getElementById(selector)
    }
    if(key=="animation"){
        return document.getAnimations(selector)
    }
    if(key=="class"){
        return document.getElementsByClassName(selector)
    }
    if(key=="name"){
        return document.getElementsByName(selector)
    }
    if(key=="tag"){
        return document.getElementsByTagName(selector)
    }
    if(key=="tagns"){
        return document.getElementsByTagNameNS(selector)
    }
    if(key=="qtor"){
        return document.querySelector(selector)
    }
    if(key=="all"){
        return document.querySelectorAll(selector)
    }
    conlog("[ERROR]dget key not found"+key)
}

function docgetid(selector){ // document.getElementById
    return document.getElementById(selector)
}

function docgetall(selector){ // document.querySelectorAll
    return document.querySelectorAll(selector)
}

function conlog(data){ // consloe.log 值出來
    console.log(data)
}

function isset(data){ // 看值是否存在
    if(data!=null||data!=undefined){ return true }
    else{ return false }
}

function blank(data){ // 看值是否不為空
    if(data!=null||data!=undefined||data!=""){ return true }
    else{ return false }
}

function weblsset(data,value){ // localStorage.setItem
    return localStorage.setItem(data,value)
}

function weblsget(data){ // localStorage.getItem
    return localStorage.getItem(data)
}