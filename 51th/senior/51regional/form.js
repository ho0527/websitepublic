let count=0
let id=row[0]
let maincount
let oldmaxcount=docgetid("maxcount").value
let oldpagelen=docgetid("pagelen").value

function checknull(data){
    if(data==null||data==undefined||data==""){ return true }
    else{ return false }
}

function pregmatch(context,data){ return context.test(data) }

function newquestion(){
    questionrow.push([(count+1).toString(),"","false","none","",false,""])
    maincount=maincount+1
    count=count+1
    docgetid("maindiv").innerHTML=docgetid("maindiv").innerHTML+`
    <div class="questionmain grid" id="${count}">
        <div class="order">
            <div class="questiondel" data-id="${count}">X</div>
            <div id="count${count}">${count}</div>
        </div>
        <div class="newform">
            <select class="modselect stselect large fluid textcenter sttext big" id="modselete${count}" data-id="${count}">
                <option value="none">未設定</option>
                <option value="yesno">是非題</option>
                <option value="single">單選題</option>
                <option value="multi">多選題</option>
                <option value="qa">問答題</option>
            </select>
        </div>
        <div class="output textcenter">
            <div class="questiondiv" id="output${count}"></div>
        </div>
    </div>
    `
    docgetid("count").value=count
    divsort("grid","#maindiv")
}

function tempsave(){
    questionrow=[]
    docgetall(".questionmain").forEach(function(event){
        let mod=docgetid("modselete"+event.id).value
        let showmultimoreresponse=false
        let option=""
        let description=""
        let required=false

        if(mod!="none"){
            option=""
            if(mod=="single"||mod=="multi"){
                docgetall(".option"+event.id).forEach(function(event){
                    if(!checknull(event.value)){
                        if(event.value!="|&|"){ option=option+event.value+"|&|" }
                        else{ alert("選項禁止輸入|&| 位於第"+event.id+"欄，故選項不儲存") }
                    }
                })
            }
        }

        if(docgetid("showmultimoreresponse"+event.id)){
            if(docgetid("showmultimoreresponse"+event.id).checked){
                showmultimoreresponse=true
            }
        }

        if(docgetid("description"+event.id)){
            description=docgetid("description"+event.id).value
        }

        if(docgetid("required"+event.id)){
            required=docgetid("required"+event.id).checked
        }

        questionrow.push([questionrow.length+1,description,required,mod,option,showmultimoreresponse,""])
    })
}

function save(){
    let insertdata=[]
    let maxcount=docgetid("maxcount").value
    let pagelen=docgetid("pagelen").value
    if(maxcount=="無上限"){
        maxcount=""
    }
    if(!pregmatch(/[0-9]+/,maxcount)&&maxcount!=""){
        alert("最大長度只能是數字或空白")
        maxcount=oldmaxcount
    }
    if(!pregmatch(/[0-9]+/,pagelen)){
        alert("頁面長度只能是數字")
        pagelen=oldpagelen
    }
    insertdata.push([id,docgetid("title").value,count,pagelen,maxcount])
    tempsave()
    insertdata.push(questionrow)
    ajax("POST","api/newform.php",function(event){
        let data=JSON.parse(event.responseText)
        if(data["success"]){
            alert("儲存成功")
            weblsset("51regionalscrolllocation",docgetid("maindiv").scrollTop)
            location.reload()
        }else{
            alert("出現不明錯誤")
        }
    },JSON.stringify(insertdata),[["Content-Type","application/json"]])
}

function main(){
    count=0
    docgetid("maindiv").innerHTML=``
    for(let i=0;i<maincount;i=i+1){
        let mod={
            "none":"未設定",
            "yesno":"是非題",
            "single":"單選題",
            "multi":"多選題",
            "qa":"問答題",
        }
        let modkey=Object.keys(mod)
        let all=""
        let check=0
        let output=""
        let selectinnerhtml=`
            <select class="modselect stselect large fluid textcenter sttext big" id="modselete${i}" data-id="${i}">
                <option value="none">未設定</option>
                <option value="yesno">是非題</option>
                <option value="single">單選題</option>
                <option value="multi">多選題</option>
                <option value="qa">問答題</option>
            </select>
        `

        if(questionrow==""){
            for(let j=0;j<modkey.length;j=j+1){
                let type=modkey[j]
                let checked=""
                if(type=="none"){
                    checked="checked"
                    check=1
                }
                all=all+"<input type='radio' class='radio "+type+" select"+i+"' data-id='"+type+" "+i+"' name='select"+i+"' value='"+type+"' "+checked+">"+mod[modkey[j]]
            }
        }else{
            for(let j=0;j<modkey.length;j=j+1){
                let type=modkey[j];
                let checked="";
                if(questionrow[i][3]==undefined){
                    if(type=="none"){
                        checked="checked"
                        check=1
                    }
                }else{
                    if(type==questionrow[i][3]){
                        checked="checked"
                        check=1
                    }
                }
                all=all+"<input type='radio' class='radio "+type+" select"+i+"' data-id='"+type+" "+i+"' name='select"+i+"' value='"+type+"' "+checked+">"+mod[modkey[j]]
            }
        }

        if(check!=1){ sql001();location.href="admin.php" }

        if(questionrow==""||questionrow[i][3]==undefined||questionrow[i][3]=="none"){
            output="<input type='hidden' class='description required showmultimoreresponse option"+i+"' id='description"+i+"'>"
        }else{
            let option=questionrow[i][4].split("|&|")
            if(questionrow[i][2]==true){
                output=output+"必填<input type='checkbox' class='required' id='required"+i+"' checked><br>"
            }else{
                output=output+"必填<input type='checkbox' class='required' id='required"+i+"'><br>"
            }
            output=output+"題目說明:<input type='text' class='description' id='description"+i+"' value='"+questionrow[i][1]+"'><br>"
            if(questionrow[i][3]=="yesno"){
                output=output+"是<input type='radio' class='yesno' name='yesno' value='yes' disabled>否<input type='radio' name='yesno' value='no' disabled><br><input type='hidden' class='showmultimoreresponse option"+i+"'>"
                selectinnerhtml=`
                    <select class="modselect stselect large fluid textcenter sttext big" id="modselete${i}" data-id="${i}">
                        <option value="none">未設定</option>
                        <option value="yesno" selected>是非題</option>
                        <option value="single">單選題</option>
                        <option value="multi">多選題</option>
                        <option value="qa">問答題</option>
                    </select>
                `
            }else if(questionrow[i][3]=="single"){
                for(let j=0;j<6;j=j+1){
                    if(checknull(option[j])){
                        output=output+(j+1+".<input type='text' class='option"+i+"' value=''>")
                    }else{
                        output=output+(j+1+".<input type='text' class='option"+i+"' value='"+option[j]+"'>")
                    }
                }
                selectinnerhtml=`
                    <select class="modselect stselect large fluid textcenter sttext big" id="modselete${i}" data-id="${i}">
                        <option value="none">未設定</option>
                        <option value="yesno">是非題</option>
                        <option value="single" selected>單選題</option>
                        <option value="multi">多選題</option>
                        <option value="qa">問答題</option>
                    </select>
                `
            }else if(questionrow[i][3]=="multi"){
                for(let j=0;j<6;j=j+1){
                    if(checknull(option[j])){
                        output=output+(j+1+".<input type='text' class='option"+i+"' value=''>")
                    }else{
                        output=output+(j+1+".<input type='text' class='option"+i+"' value='"+option[j]+"'>")
                    }
                }
                if(questionrow[i][5]==true||questionrow[i][5]==undefined){
                    output=output+"<br>顯示其他選項<input type='checkbox' class='showmultimoreresponse' id='showmultimoreresponse"+i+"' checked>"
                }else{
                    output=output+"<br>顯示其他選項<input type='checkbox' class='showmultimoreresponse' id='showmultimoreresponse"+i+"'>"
                }
                selectinnerhtml=`
                    <select class="modselect stselect large fluid textcenter sttext big" id="modselete${i}" data-id="${i}">
                        <option value="none">未設定</option>
                        <option value="yesno>是非題</option>
                        <option value="single">單選題</option>
                        <option value="multi" selected>多選題</option>
                        <option value="qa">問答題</option>
                    </select>
                `
            }else if(questionrow[i][3]=="qa"){
                output=output+"<textarea cols='30' rows='2' placeholder='問答題' disabled></textarea><br><input type='hidden' class='showmultimoreresponse option"+i+"'>"
                selectinnerhtml=`
                    <select class="modselect stselect large fluid textcenter sttext big" id="modselete${i}" data-id="${i}">
                        <option value="none">未設定</option>
                        <option value="yesno>是非題</option>
                        <option value="single">單選題</option>
                        <option value="multi">多選題</option>
                        <option value="qa" selected>問答題</option>
                    </select>
                `
            }else{ sql001();location.href="admin.php" }
        }

        docgetid("maindiv").innerHTML=docgetid("maindiv").innerHTML+`
            <div class="questionmain grid" id="${i}">
                <div class="order">
                    <div class="questiondel" data-id="${i}">X</div>
                    <div id="count${i}">${i+1}</div>
                </div>
                <div class="newform">
                    ${selectinnerhtml}
                </div>
                <div class="output textcenter">
                    <div class="questiondiv" id="output${i}">
                        ${output}
                    </div>
                </div>
            </div>
        `

        count=count+1
    }

    docgetid("maindiv").scrollTop=weblsget("51regionalscrolllocation")
}

if(!weblsget("51regionalscrolllocation")){
    weblsset("51regionalscrolllocation",0)
}

if(!checknull(questionrow)){
    questionrow=Object.values(JSON.parse(questionrow))
    maincount=questionrow.length
}else{
    maincount=parseInt(row[2])
}

main()
divsort("grid","#maindiv")

docgetid("title").value=row[1]
docgetid("count").value=count
docgetid("pagelen").value=row[3]
if(row[6]!=""){
    docgetid("maxcount").value=row[6]
}else{
    docgetid("maxcount").value="無上限"
}

docgetall(".modselect").forEach(function(event){
    event.onchange=function(){
        let id=this.dataset.id
        let data=this.value
        let option=questionrow[id][4].split("|&|")
        let output=""

        if(questionrow[id][2]==true){
            output=output+"必填<input type='checkbox' class='required' id='required"+id+"' checked><br>"
        }else{
            output=output+"必填<input type='checkbox' class='required' id='required"+id+"'><br>"
        }

        output=output+"題目說明:<input type='text' class='description' id='description"+id+"' name='direction"+id+"' value='"+questionrow[id][1]+"'><br>"

        if(data=="none"){
            output="<input type='hidden' class='description required showmultimoreresponse option"+id+"'>"
        }else if(data=="yesno"){
                output=output+"是<input type='radio' class='yesno' name='yesno' value='yes' disabled>否<input type='radio' name='yesno' value='no' disabled><br><input type='hidden' class='showmultimoreresponse option"+id+"'>"
        }else if(data=="single"){
            for(let j=0;j<6;j=j+1){
                if(checknull(option[j])){
                    output=output+(j+1+".<input type='text' class='option"+id+"' value=''>")
                }else{
                    output=output+(j+1+".<input type='text' class='option"+id+"' value='"+option[j]+"'>")
                }
            }
        }else if(data=="multi"){
            for(let j=0;j<6;j=j+1){
                if(checknull(option[j])){
                    output=output+(j+1+".<input type='text' class='option"+id+"' value=''>")
                }else{
                    output=output+(j+1+".<input type='text' class='option"+id+"' value='"+option[j]+"'>")
                }
            }
            if(questionrow[id][5]==true||questionrow[id][5]==undefined){
                output=output+"<br>顯示其他選項<input type='checkbox' class='showmultimoreresponse' checked>"
            }else{
                output=output+"<br>顯示其他選項<input type='checkbox' class='showmultimoreresponse'>"
            }
        }else if(data=="qa"){
            output=output+"<textarea cols='30' rows='2' placeholder='問答題' disabled></textarea><br><input type='hidden' class='showmultimoreresponse option"+id+"'>"
        }else{ sql001();location.href="admin.php" }

        docgetid("output"+id).innerHTML=output
    }
})

docgetall(".questiondel").forEach(function(event){
    event.onclick=function(){
        let id=event.dataset.id
        docgetid(id).remove()
        tempsave()
    }
})

document.addEventListener("keydown",function(event){
    if(event.key=="Escape"){
        location.href="api.php?cancel="
    }
    if(event.ctrlKey&&event.key=="s"){
        event.preventDefault()
        save()
    }
})

startmacossection()