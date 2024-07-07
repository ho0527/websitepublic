let id=row[0]
let insertdata=[]
let questionrownotnone=[]
let maxlen=row[3]
let page=0
let maxpage

/*
questionrow:
questionid,description,required,mod,option,showmultimoreresponse,ps
*/

function checknull(data){
    if(data==null||data==undefined||data==""){ return true }
    else{ return false }
}

function pregmatch(context,data){ return context.test(data) }

function tempsave(){
    let success=true
    let errorlist=[]
    let tempdata=[]

    docgetall(".questionmain").forEach(function(event){
        let id=event.id
        let no=event.querySelector(".order>.count").innerHTML
        let tempmod=event.dataset.mod
        let description=event.dataset.description
        let required=event.dataset.required
        let showmultimoreresponse=""
        let response=null
        let mod=""

        if(tempmod=="yesno"){
            if(docgetid(id+"yes").checked){
                response=true
            }else if(docgetid(id+"no").checked){
                response=false
            }
            mod="yesno"
        }else if(tempmod=="single"){
            let option=[]
            for(let j=0;j<6;j=j+1){
                if(docgetid(id+"option"+j)){
                    if(docgetid(id+"option"+j).checked){
                        option.push(docgetid(id+"option"+j).value)
                    }
                }
            }
            if(option.length>0){
                response=option.join("|&|")
            }
            mod="single"
        }else if(tempmod=="multi"){
            let option=[]
            for(let j=0;j<6;j=j+1){
                if(docgetid(id+"option"+j)){
                    if(docgetid(id+"option"+j).checked){
                        option.push(docgetid(id+"option"+j).value)
                    }
                }
            }
            if(option.length>0){
                response=option.join("|&|")
            }
            mod="multi"
        }else{
            if(docgetid("qa"+id).value!=""){
                response=docgetid("qa"+id).value
            }
            mod="qa"
        }

        if(required=="true"){
            if(response==null){
                success=false
                errorlist.push(no)
            }
        }

        if(docgetid("showmultimoreresponse"+id)){
            showmultimoreresponse=docgetid("showmultimoreresponse"+id).innerHTML
        }

        tempdata.push([no,description,required,mod,response,showmultimoreresponse,""])
    })

    if(success){
        for(let i=0;i<tempdata.length;i=i+1){
            let check=false
            for(let j=0;j<insertdata.length;j=j+1){
                if(insertdata[j][0]==tempdata[i][0]){
                    insertdata[j]=tempdata[i]
                    check=true
                }
            }
            if(!check){
                insertdata.push(tempdata[i])
            }
        }
        return true
    }else{
        alert("第"+errorlist.join(" ")+"題為必填但未填寫")
        return false
    }
}

function save(){
    if(page==maxpage-1){
        if(tempsave()){
            // 送出資料
            ajax("POST","api/newresponse.php",function(event){
                let data=JSON.parse(event.responseText)
                if(data["success"]){
                    alert("送出成功感謝您的填寫!~")
                    location.href="index.php"
                }
            },JSON.stringify({
                "userid": userid,
                "questionid": row[0],
                "response": insertdata
            }),[])
        }
    }else{
        alert("請先翻到最後一頁再送出")
    }
}

function main(){
    ajax("GET","api.php?getquestion=&id="+id+"&page="+page+"&maxlen="+maxlen,function(event){
        let data=JSON.parse(event.responseText)

        if(data["success"]){
            let row=data["data"]

            count=0
            maxpage=data["maxpage"]
            docgetid("maindiv").innerHTML=``
        
            // 輸出每一個題目 START
            for(let i=0;i<row.length;i=i+1){
                let required="false"
                let option=row[i][4].split("|&|")
                let output=""
                let requiredinnerhtml=""
                let modname
                
                if(row[i][2]==true){
                    requiredinnerhtml="<div class='required'>必填*</div>"
                    required="true"
                }
        
                output=output+"題目說明: "+row[i][1]+"<br>"
        
                if(row[i][3]=="yesno"){
                    output=`
                        ${output}
                        <label class="label" for="${i}yes">是</label>
                        <input type="radio" class="yesno radio" id="${i}yes" name="yesno${i}" value="yes">
                        <label class="label" for="${i}no">否</label>
                        <input type="radio" class="radio" id="${i}no" name="yesno${i}" value="no">
                    `
                    modname="是非題"
                }else if(row[i][3]=="single"){
                    for(let j=0;j<6;j=j+1){
                        if(!checknull(option[j])){
                            output=`
                                ${output}
                                <label class="label" for="${i+"option"+j}">${option[j]}</label>
                                <input type="radio" class="radio option${i}" id="${i+"option"+j}" name="single${i}" value="${option[j]}">
                            `
                        }
                    }
                    modname="單選題"
                }else if(row[i][3]=="multi"){
                    for(let j=0;j<6;j=j+1){
                        if(!checknull(option[j])){
                            output=`
                                ${output}
                                <label class="label" for="${i+"option"+j}">${option[j]}</label>
                                <input type="checkbox" class="checkbox option${i}" id="${i+"option"+j}" value="${option[j]}">
                            `
                        }
                    }
                    if(row[i][5]==true){
                        output=`
                            ${output}<br>
                            其他: <input type='text' class="forminputtext" id="multimoreresponse${i}" name="multiauther"+i+"">
                        `
                    }
                    modname="多選題"
                }else if(row[i][3]=="qa"){
                    output=`
                        ${output}
                        <div class="stinput textarea question">
                            <textarea id="qa${i}" placeholder="問答題"></textarea>
                        </div>
                    `
                    modname="問答題"
                }else{ sql001();location.href="admin.php" }
        
                docgetid("maindiv").innerHTML=docgetid("maindiv").innerHTML+`
                    <div class="questionmain grid" id="${i}" data-required="${required}" data-mod="${row[i][3]}" data-description="${row[i][1]}">
                        <div class="order">
                            ${requiredinnerhtml}
                            <div class="count" id="count${i}">${row[i][0]}</div>
                        </div>
                        <div class="newform">
                            ${modname}
                        </div>
                        <div class="textcenter output">
                            <div class="questiondiv" id="output${i}">
                                ${output}
                            </div>
                        </div>
                    </div>
                `
            }
        }else{
            alert(data["data"])
            location.href="admin.php"
        }
    })
    // 輸出每一個題目 END
}

docgetid("id").value=row[0]
docgetid("title").value=row[1]
docgetid("count").value=row[2]

if(userkey=="true"){
    main()
}

docgetid("prev").onclick=function(){
    if(0<page){
        page=page-1
        docgetid("progress").style.width=parseInt((page/maxpage)*100)+"%"
        docgetid("progresstext").innerHTML=`${parseInt((page/maxpage)*100)}/100%`
        tempsave()
        main()
    }else{
        alert("已在最前一頁")
    }
}

docgetid("next").onclick=function(){
    if(page+1<maxpage&&tempsave()){
        page=page+1
        docgetid("progress").style.width=parseInt((page/maxpage)*100)+"%"
        docgetid("progresstext").innerHTML=`${parseInt((page/maxpage)*100)}/100%`
        main()
    }else{
        alert("已到最後一頁")
    }
}

document.onkeydown=function(event){
    if(event.key=="Escape"){
        location.href="api.php?cancel="
    }
    if(event.ctrlKey&&event.key=="s"){
        event.preventDefault()
        check()
    }
}

startmacossection()