/*
    標題: 拖拉排序
    參考:
    作者: 小賀chris
    製作及log:
    2023/06/10  02:02:10 BATA 1.0.0 // 拖拉完成 等待製作儲存data
    2023/06/10  18:11:15 BATA 1.1.0 // bug fix

        |-------    -----    -                     -     -----  -----  -----   -------|
       |-------    -        -            - - -          -                     -------|
      |-------    -        -------    -          -     -----    --       --  -------|
     |-------    -        -     -    -          -         -      --     --  -------|
    |-------    -----    -     -    -          -     -----         -----  -------|
*/

function sort(card,cardclass,sortdiv){ // card 放要被拖的物件 cardclass 放要被拖的物件的class sortdiv 放要放的物件
    let data=[]
    let copy

    document.querySelectorAll(card).forEach(function(event){
        event.draggable="true"
    })

    document.querySelectorAll(sortdiv).forEach(function(event){
        event.ondragstart=function(addeventlistenerevent){
            addeventlistenerevent.target.classList.add("dragging")
        }

        event.ondragover=function(addeventlistenerevent){
            addeventlistenerevent.preventDefault()
            let sortableContainer=addeventlistenerevent.target.closest(sortdiv)
            if(sortableContainer){
                let draggableElements=Array.from(sortableContainer.children).filter(function(child){
                    return child.classList.contains(cardclass)&&!child.classList.contains("dragging")
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

                let draggable=document.querySelector(".dragging")
                if(afterElement==null){
                    sortableContainer.appendChild(draggable)
                }else{
                    sortableContainer.insertBefore(draggable,afterElement)
                }
            }
        }

        event.ondragend=function(addeventlistenerevent){
            addeventlistenerevent.target.classList.remove("dragging")
            let count=0
            document.querySelectorAll(".grid").forEach(function(event){
                let id=event.id
                document.querySelectorAll(".questiondel")[count].dataset.id=count
                document.getElementById("count"+id).innerHTML=`${count+1}`
                document.getElementById("count"+id).id="tempcount"+count
                document.getElementById("output"+id).id="tempoutput"+count
                document.querySelectorAll(".select"+id).forEach(function(event){
                    event.classList.remove("select"+id)
                    event.classList.add("tempselect"+count)
                })
                document.querySelectorAll(".option"+id).forEach(function(event){
                    event.classList.remove("option"+id)
                    event.classList.add("tempoption"+count)
                })
                event.id=count
                count=count+1
            })
            for(let i=0;i<count;i=i+1){
                document.getElementById("tempcount"+i).id="count"+i
                document.getElementById("tempoutput"+i).id="output"+i
                document.querySelectorAll(".tempselect"+i).forEach(function(event){
                    event.classList.remove("tempselect"+i)
                    event.classList.add("select"+i)
                })
                document.querySelectorAll(".tempoption"+i).forEach(function(event){
                    event.classList.remove("tempoption"+i)
                    event.classList.add("option"+i)
                })
            }
            tempsave()
        }
    })

    document.querySelectorAll(card).forEach(function(event){
        data.push(event)
    })

    return data
}