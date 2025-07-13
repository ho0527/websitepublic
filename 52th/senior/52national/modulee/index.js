let db=null
let students=[]
let classes=[]

function openDB(){
    let req=indexedDB.open("studentdb",2)

    req.onupgradeneeded=function(e){
        db=e.target.result
        if(!db.objectStoreNames.contains("students")){
            db.createObjectStore("students",{keyPath:"id"})
        }
        if(!db.objectStoreNames.contains("trash")){
            db.createObjectStore("trash",{keyPath:"id"})
        }
        if(!db.objectStoreNames.contains("classes")){
            db.createObjectStore("classes",{keyPath:"name"})
        }
    }

    req.onsuccess=function(e){
        db=e.target.result
        loadStudents()
        loadClasses()
    }
}

function loadStudents(){
    let tx=db.transaction("students","readonly")
    let store=tx.objectStore("students")
    let req=store.getAll()

    req.onsuccess=function(e){
        students=req.result
        renderStudents()
    }
}

function loadClasses(){
    let tx=db.transaction("classes","readonly")
    let store=tx.objectStore("classes")
    let req=store.getAll()

    req.onsuccess=function(e){
        classes=req.result
    }
}

function saveStudent(data){
    let tx=db.transaction("students","readwrite")
    let store=tx.objectStore("students")
    store.put(data)
    tx.oncomplete=function(){
        loadStudents()
    }
}

function deleteStudent(id){
    let tx1=db.transaction("students","readwrite")
    let store1=tx1.objectStore("students")
    let req=store1.get(id)
    req.onsuccess=function(){
        let data=req.result
        let tx2=db.transaction("trash","readwrite")
        tx2.objectStore("trash").put(data)
        store1.delete(id)
        tx2.oncomplete=function(){
            loadStudents()
        }
    }
}

function generateID(){
    return "S"+Date.now()
}

function toBase64(file,callback){
    let reader=new FileReader()
    reader.onload=function(){
        callback(reader.result)
    }
    reader.readAsDataURL(file)
}

function handleStudentSubmit(form,dialog,origin){
    let id=origin?origin.id:generateID()
    let lastname=form.last_name.value
    let firstname=form.first_name.value
    let email=[form.querySelector("input[name='email[]']").value]
    let phone=[form.querySelector("input[name='phone[]']").value]
    let address=form.address.value
    let cls=form.class.value
    let note=form.note.value
    let file=dialog.querySelector(".avatar").files[0]
    let avatarInput=form.querySelector("input.avatar")

    let save=function(base64){
        saveStudent({id:id,lastname:lastname,firstname:firstname,email:email,phone:phone,address:address,class:cls,avatar:base64,note:note})
        document.body.removeChild(dialog)
    }

    if(file){
        toBase64(file,function(b64){
            save(b64)
        })
    }else{
        save(origin?origin.avatar:"default.jpeg")
    }
}

document.addEventListener("DOMContentLoaded",function(){
    document.getElementById("addStudent").onclick=function(){
        showStudentDialog()
    }
})

function renderStudents(){
    let container=document.querySelector(".students")
    container.innerHTML=""

    if(students.length==0){
        let msg=document.createElement("p")
        msg.className="message text-gray-500"
        msg.textContent="目前還沒有任何學生"
        container.appendChild(msg)
        return
    }

    let table=document.createElement("table")
    table.className="w-full text-left bg-white rounded shadow"

    let thead=document.createElement("thead")
    let hr=document.createElement("tr")
    let headers=["大頭貼","姓名","學號","Email","電話","班級","地址","功能"]
    for(let i=0;i<headers.length;i=i+1){
        let th=document.createElement("th")
        th.className="px-4 py-2 border-b"
        th.textContent=headers[i]
        hr.appendChild(th)
    }
    thead.appendChild(hr)

    let tbody=document.createElement("tbody")
    for(let i=0;i<students.length;i=i+1){
        let s=students[i]

        let tr=document.createElement("tr")
        tr.className="student hover:bg-gray-50"

        let avatar=document.createElement("td")
        avatar.className="px-4 py-2"
        let img=document.createElement("img")
        img.src=s.avatar
        img.className="avatar w-[56px] h-[56px] rounded-full"
        avatar.appendChild(img)

        let fn=document.createElement("td")
        fn.className="fullname px-4 py-2"
        fn.textContent=s.lastname+s.firstname

        let sid=document.createElement("td")
        sid.className="student_id px-4 py-2"
        sid.textContent=s.id

        let mail=document.createElement("td")
        mail.className="email px-4 py-2"
        mail.textContent=s.email[0]||""

        let tel=document.createElement("td")
        tel.className="phone px-4 py-2"
        tel.textContent=s.phone[0]||""

        let cls=document.createElement("td")
        cls.className="class px-4 py-2"
        cls.textContent=s.class

        let addr=document.createElement("td")
        addr.className="address px-4 py-2"
        addr.textContent=s.address

        let act=document.createElement("td")
        act.className="actions px-4 py-2"
        let btnbox=document.createElement("div")
        btnbox.className="flex space-x-2"
        let edit=document.createElement("button")
        edit.className="edit text-blue-500"
        edit.textContent="編輯"
        edit.onclick=function(){
            showStudentDialog(s)
        }
        let del=document.createElement("button")
        del.className="delete text-red-500"
        del.textContent="刪除"
        del.onclick=function(){
            deleteStudent(s.id)
        }
        btnbox.appendChild(edit)
        btnbox.appendChild(del)
        act.appendChild(btnbox)

        tr.appendChild(avatar)
        tr.appendChild(fn)
        tr.appendChild(sid)
        tr.appendChild(mail)
        tr.appendChild(tel)
        tr.appendChild(cls)
        tr.appendChild(addr)
        tr.appendChild(act)

        tbody.appendChild(tr)
    }

    table.appendChild(thead)
    table.appendChild(tbody)
    container.appendChild(table)
}

function showClassDialog(){
    let dialog=document.createElement("div")
    dialog.id="dialog"
    dialog.className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"

    dialog.innerHTML=`
    <div class="bg-white p-6 rounded shadow w-full max-w-sm">
        <h2 class="title text-xl font-bold mb-4">建立班級</h2>
        <form class="newClass space-y-4">
            <div>
                <label class="block text-sm mb-1">班級名稱</label>
                <input name="name" class="w-full border px-2 py-1" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" class="close bg-gray-300 px-4 py-1 rounded">取消</button>
                <button type="submit" class="submit bg-blue-500 text-white px-4 py-1 rounded">儲存</button>
            </div>
        </form>
    </div>`

    dialog.querySelector(".close").onclick=function(){
        document.body.removeChild(dialog)
    }

    let form=dialog.querySelector("form")
    form.onsubmit=function(e){
        e.preventDefault()
        let name=form.name.value.trim()
        if(name=="")return
        let tx=db.transaction("classes","readwrite")
        tx.objectStore("classes").put({name:name})
        tx.oncomplete=function(){
            document.body.removeChild(dialog)
            loadClasses()
        }
    }

    document.body.appendChild(dialog)
}

document.addEventListener("DOMContentLoaded",function(){
    document.getElementById("addStudent").onclick=function(){
        showStudentDialog()
    }
    document.getElementById("addClass").onclick=function(){
        showClassDialog()
    }
})

function updateSidebarCount(){
    let studentCount=students.length
    let all=document.querySelector("#studentList .num")
    if(all){
        all.textContent=studentCount
    }
}

function loadStudents(){
    let tx=db.transaction("students","readonly")
    let store=tx.objectStore("students")
    let req=store.getAll()

    req.onsuccess=function(e){
        students=req.result
        renderStudents()
        updateSidebarCount()
    }
}

function showStudentDialog(origin){
    let dialog=document.createElement("div")
    dialog.id="dialog"
    dialog.className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"

    dialog.innerHTML=`
        <div class="bg-white p-6 rounded shadow w-full max-w-xl">
            <h2 class="title text-xl font-bold mb-4">${origin?"編輯學生":"建立學生"}</h2>
            <form class="newStudent space-y-4">
                <div>
                    <label class="block text-sm mb-1">大頭貼</label>
                    <input type="file" class="avatar" accept=".jpg,.jpeg,.png">
                    <img class="avatar_preview w-[120px] h-[120px] rounded mt-2" src="${origin?origin.avatar:"default.jpeg"}">
                </div>
                <div>
                    <label class="block text-sm mb-1">姓氏</label>
                    <input name="last_name" class="w-full border px-2 py-1" required value="${origin?origin.lastname:""}">
                </div>
                <div>
                    <label class="block text-sm mb-1">名字</label>
                    <input name="first_name" class="w-full border px-2 py-1" required value="${origin?origin.firstname:""}">
                </div>
                <div>
                    <label class="block text-sm mb-1">電子郵件</label>
                    <input name="email[]" type="email" class="w-full border px-2 py-1" value="${origin?origin.email[0]:""}">
                </div>
                <div>
                    <label class="block text-sm mb-1">電話</label>
                    <input name="phone[]" type="tel" class="w-full border px-2 py-1" value="${origin?origin.phone[0]:""}">
                </div>
                <div>
                    <label class="block text-sm mb-1">地址</label>
                    <input name="address" class="w-full border px-2 py-1" value="${origin?origin.address:""}">
                </div>
                <div>
                    <label class="block text-sm mb-1">班級</label>
                    <select name="class" class="w-full border px-2 py-1"></select>
                </div>
                <div>
                    <label class="block text-sm mb-1">備註</label>
                    <textarea name="note" class="w-full border px-2 py-1" value="${origin?origin.note:""}">${origin?origin.note:""}</textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="close bg-gray-300 px-4 py-1 rounded">取消</button>
                    <button type="submit" class="submit bg-blue-500 text-white px-4 py-1 rounded">儲存</button>
                </div>
            </form>
        </div>
    `

    dialog.querySelector(".close").onclick=function(){
        document.body.removeChild(dialog)
    }

    let form=dialog.querySelector("form")
    form.onsubmit=function(e){
        e.preventDefault()
        handleStudentSubmit(form,dialog,origin)
    }

    dialog.querySelector(".avatar").onchange=function(e){
        let file=e.target.files[0]
        if(file){
            toBase64(file,function(b64){
                dialog.querySelector(".avatar_preview").src=b64
            })
        }
    }

    let select=dialog.querySelector("select[name='class']")
    for(let i=0;i<classes.length;i=i+1){
        let opt=document.createElement("option")
        opt.value=classes[i].name
        opt.textContent=classes[i].name
        if(origin&&origin.class==classes[i].name){
            opt.selected=true
        }
        select.appendChild(opt)
    }

    document.body.appendChild(dialog)
}
openDB()
