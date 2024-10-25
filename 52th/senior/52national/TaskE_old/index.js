let editstu=document.querySelectorAll(".edit")
let dialog=document.getElementById("dialog")
let addsut=document.getElementById("addStudent")
let newstudent=document.getElementById("newStudent")
let lessmoreclass=document.getElementById("allclass")
let allclass=document.getElementById("class")
let addclass=document.getElementById("addClass")
let allstu=document.getElementById("allstu")
let delstu=document.getElementById("delete")
let main=document.getElementById("main")
let trashcan=document.getElementById("trashcan")
let studentdb
let classdb

function openstudentdb(){
    return new Promise(function(){
        let dbrequest=indexedDB.open("student",1)

        dbrequest.onerror=function(){
            console.log("the db can\`t open no "+dbrequest.errorCode)
        }

        dbrequest.onsuccess=function(event){
            console.log("Database students opened successfully.")
            studentdb=event.target.result
        }

        dbrequest.onupgradeneeded =function(event){
            console.log("onupgradeneeded");
            studentdb=event.target.result
            let objectstore=studentdb.createObjectStore("student",{ keyPath:"id" })
            objectstore.createIndex("head","head",{ unique:false })
            objectstore.createIndex("lastname","lastname",{ unique:false })
            objectstore.createIndex("firstname","firstname",{ unique:false })
            objectstore.createIndex("email","email",{ unique:false })
            objectstore.createIndex("phone","phone",{ unique:false })
            objectstore.createIndex("address","address",{ unique:false })
            objectstore.createIndex("class","class",{ unique:false })
        }
    })
}

function openclassdb(){
    return new Promise(function(){
        let dbrequest=indexedDB.open("class",1)

        dbrequest.onerror=function(){
            console.log("the db can\`t open no "+dbrequest.errorCode)
        }

        dbrequest.onsuccess=function(event){
            console.log("Database class opened successfully.")
            classdb=event.target.result
        }

        dbrequest.onupgradeneeded=function(event){
            classdb=event.target.result
            let objectstore=classdb.createObjectStore("class",{ keyPath:"id" })
            objectstore.createIndex("name","name",{ unique:false })
            objectstore.createIndex("count","count",{ unique:false })
        }
    })
}

function dbinsert(db,dbname,insertdata){ // 資料庫名 要輸入的值(使用json)
    let objectstore=db
        .transaction(dbname,"readwrite")
        .objectStore(dbname)
    console.log(insertdata)
    let request=objectstore.add(insertdata)

    request.onsuccess=function(){
        console.log(dbname+" data= "+insertdata+" added to the database success")
    }

    request.onerror=function(){
        console.log("[WARNING]"+dbname+" data= "+insertdata+" added to the database error")
    }
}

function dbupdate(db,dbname,updatefield,updatedata,updatevalue){ // 資料庫名 要改的欄位(int) 要更新的欄位 改成的值
    let objectstore=db
        .transaction(dbname,"readwrite")
        .objectStore(dbname)
    let request=objectstore.get(updatefield)

    request.onsuccess=function(event){
        let result=event.target.result
        result[updatedata]=updatevalue
        let updaterequest=objectstore.put(result)
        updaterequest.onsuccess=function(){
            console.log(dbname+" update success")
        }
    }

    request.onerror=function(){
        console.log("[WARNING]"+dbname+" update error")
    }
}

function dbdelete(db,dbname,deletedata){ // 資料庫名 刪除的欄位(int)
    let objectstore=db
        .transaction(dbname,"readwrite")
        .objectStore(dbname)
    let request=objectstore.delete(deletedata)

    request.onsuccess=function(){
        console.log(dbname+" delete success")
    }

    request.onerror=function(){
        console.log("[WARNING]"+dbname+" delete error")
    }
}

function dbselect(db,dbname,selectfield,selectvalue,callback){ // 資料庫名 要查詢的欄位(int) 要找的值 回傳函式
    let objectstore=db
        .transaction(dbname,"readwrite")
        .objectStore(dbname)
    let index=objectstore.index(selectfield)
    let request=index.get(selectfield)

    request.onsuccess=function(event){
        let result=event.target.result
        if(result){
            callback(result)
            console.log(selectfield+" = "+selectvalue+" select success")
        }else{
            console.log("no result")
        }
    }

    request.onerror=function(){
        console.log("[WARNING]"+dbname+" select error")
    }
}

function claerselect(){
    allstu.classList.remove("current")
    trashcan.classList.remove("current")
}

function reload(){
    claerselect()
    dialog.innerHTML=``
    location.reload()
}

window.onload=function(){
    dialog.innerHTML=``
    allclass.innerHTML=`
        <div id="class">
            <span class="num"></span>
        </div>
    `
    main.innerHTML=`
        <div class="students">
            <div class="student">
                <table>
                    <tr>
                        <td class="maintd avator">頭像</td>
                        <td class="maintd fullname">姓名</td>
                        <td class="maintd student_id">學號</td>
                        <td class="maintd email">電子郵件</td>
                        <td class="maintd phone">電話號碼</td>
                        <td class="maintd class">班級</td>
                        <td class="maintd address">地址</td>
                        <td class="maintd action">動作</td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <div class="message">目前還沒有任何學生</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    `
    allstu.classList.add("current")
    openstudentdb()
    openclassdb()
    setTimeout(function(){
        console.log(studentdb)
        console.log(classdb)
    },50)
}

//<tr>
//<td><img class="avatar" src=""></td>
//<td></td>
//<td></td>
//<td></td>
//<td></td>
//<td></td>
//<td></td>
//<td>
//<button class="edit">編輯</button>
//<button class="delete" id="delete">刪除</button>
//</td>
//</tr>

allstu.addEventListener("click",function(){
    location.reload()
})

lessmoreclass.addEventListener("click",function(){
    if(lessmoreclass.innerHTML=="班級(顯示更多)"){
        allclass.innerHTML=`
            ${dbselect(classdb,"class","name","Value", function(result) {
                console.log(result);
            })}
        `
        lessmoreclass.innerHTML=`班級(顯示更少)`
    }else{
        allclass.innerHTML=``
        lessmoreclass.innerHTML=`班級(顯示更多)`
    }
})

addsut.onclick=function(){
    dialog.innerHTML=`
        <div class="div">
            <div class="mask"></div>
            <div class="body">
                <h2 class="title">建立學生</h2><hr>
                <form class="newStudent" id="newStudent" method="post">
                    <div class="left">
                        <div>
                            <img src="default.jpeg" class="avater_preview"><br>
                            <input type="file" class="avatar" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="right">
                        <input type="text" name="last_name" id="last_name" class="input name" placeholder="姓氏">
                        *<input type="text" name="first_name" id="first_name" class="input name" placeholder="名字"><br><br>
                        <input type="email" name="email[]" class="input" placeholder="email"><br><br>
                        <input type="tel" name="phone[]" class="input" placeholder="手機"><br><br>
                        <input type="text" name="address" class="input" placeholder="住址"><br><br>
                        <select class="selectclass" id="class" name="class"></select><br><br>
                        <textarea class="note" name="note" cols="90" rows="7" placeholder="備註"></textarea><br>
                        <div class="buttondiv">
                            <button type="button" id="close" class="close">取消</button>
                            <button id="submit" name="enter" class="submit">確定</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
    addsut.classList.add("current")
    let close=document.getElementById("close")

    close.onclick=function(){
        reload()
    }

    addsut.onsubmit=function(event){
        let studentid=document.getElementById("student_id").value
        let lastname=document.getElementById("last_name").value
        let firstname=document.getElementById("first_name").value
        let email=document.getElementById("email").value
        let phone=document.getElementById("phone").value
        let class_id=document.getElementById("class").value
        let address=document.getElementById("address").value
        let student={studentid,lastname,firstname,email,phone,class_id,address}
        if(lastname.value==""||firstname.value==""){
            alert("請輸入姓名!")
        }else{
            alert("註冊成功")
        }
    }
}

editstu.onclick=function(){
    dialog.innerHTML=`
        <div class="div">
            <div class="mask"></div>
            <div class="body">
                <h2 class="title">編輯學生</h2><hr>
                <form class="newStudent" id="newStudent" method="post">
                    <div class="left">
                        <div class="avater">
                            <img src="default.jpeg" class="avater_preview">
                            <input type="file" class="avatar" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="right">
                        <input type="text" name="last_name" id="last_name" class="name" placeholder="姓氏">
                        <input type="text" name="first_name" id="first_name" class="name" placeholder="名字"><br>
                        <input type="email" name="email[]" class="input" placeholder="email"><br>
                        <input type="tel" name="phone[]" class="input" placeholder="手機"><br>
                        <input type="text" name="address" class="input" placeholder="住址"><br>
                        <select class="selectclass" id="class" name="class"></select><br><br>
                        <textarea name="note" cols="20" rows="1" class="note" placeholder="備註"></textarea><br>
                        <div class="buttondiv">
                            <button type="button" id="close" class="close">取消</button>
                            <button id="submit" name="enter" class="submit">確定</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
    addsut.classList.add("current")
    let close=document.getElementById("close")
    close.onclick=function(){
        reload()
    }
    newstudent.onsubmit=function(event){
        let studentid=document.getElementById("student_id").value
        let lastname=document.getElementById("last_name").value
        let firstname=document.getElementById("first_name").value
        let email=document.getElementById("email").value
        let phone=document.getElementById("phone").value
        let class_id=document.getElementById("class").value
        let address=document.getElementById("address").value
        let student={studentid,lastname,firstname,email,phone,class_id,address}
        if(lastname.value==""||firstname.value==""){
            alert("請輸入姓名!")
        }else{
            let request=objectStore.put(student)
            alert("新增成功")
        }
    }
}

addclass.addEventListener("click",function(){
    dialog.innerHTML=`
        <div class="div">
            <div class="mask"></div>
            <div class="addclassbody">
                <h2 class="title">建立班級</h2><hr><br>
                <form class="newClass" id="newclass">
                    <input type="text" class="input" id="classname" name="name" placeholder="班級名稱">
                    <div class="warning" id="warning"></div>
                    <div class="buttondiv">
                        <button type="button" class="close" id="close">取消</button>
                        <button type="submit" class="submit" id="submit" name="enter">確定</button>
                    </div>
                </form>
            </div>
        </div>
    `
    let classname=document.getElementById("classname")
    let close=document.getElementById("close")
    addclass.classList.add("current")
    close.onclick=function(){
        reload()
    }
    document.getElementById("newclass").onsubmit=function(){
        if(classname.value==""){
            document.getElementById("warning").innerHTML=`
                請輸入班級名稱
            `
            return false
        }else{
            try{
                dbinsert(classdb,"class",{
                    id:0,
                    name:document.getElementById("classname").value,
                    count:0
                })
                return true
            }catch(event){
                console.log(event)
                return false
            }
        }
    }
})

trashcan.addEventListener("click",function(){
    main.innerHTML=``
    claerselect()
    trashcan.classList.add("current")
    main.innerHTML=`

    `
})

window.onbeforeunload=null