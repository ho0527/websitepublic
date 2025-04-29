let page=/page=([^&]+)/.exec(location.search)
let orderaside=/orderaside=([^&]+)/.exec(location.search)
let category=/category=([^&]+)/.exec(decodeURI(location.search))
let data={
	"id": 1,
	"student": [],
	"class": []
}
let tablenotable
let tableid
let dbdata

function updatedb(data){
	let transaction=dbdata.transaction(["data"],"readwrite")
	let objectstore=transaction.objectStore("data")
	let request=objectstore.put(data)

	request.onsuccess=function(){ console.log("Data updated successfully.") }

	request.onerror=function(event){ console.error("Data updated error.",event.target.errorCode) }
}

function getdb(){
	let transaction=dbdata.transaction(["data"],"readwrite")
	let objectstore=transaction.objectStore("data")
	let request=objectstore.get(1)
	request.onsuccess=function(event){
		data=event.target.result
	}
	request.onerror=function(event){
		console.error("Data get error.",event.target.errorCode)
	}
}

function claerselect(){
    allstu.classList.remove("current")
    trashcan.classList.remove("current")
}

function main(selectclass){
    let maininnerhtml=``
    let check=false

    for(let i=0;i<data["student"];i=i+1){
        if(data["student"]["classname"]==selectclass["classname"]){
            maininnerhtml=`
                ${maininnerhtml}
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
            `
            check=true
        }
    }

    document.getElementById("main").innerHTML=`
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
                            ${
                                check?maininnerhtml:`<div class="message">目前還沒有任何學生</div>`
                            }
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    `

    getdb()

    setTimeout(function(){
        console.log(data["student"])
        console.log(data["class"])
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

// allstu.addEventListener("click",function(){
//     location.reload()
// })

document.getElementById("allclass").addEventListener("click",function(){
    if(document.getElementById("allclass").innerHTML=="班級(顯示更多)"){
        document.getElementById("class").innerHTML=``

        for(let i=0;i<data["class"].length;i=i+1){
            document.getElementById("class").innerHTML=`
                ${document.getElementById("class").innerHTML}
                <li class="item classitem" data-id="${i}">
                    <div class="classname">${data["class"][i]["name"]}</div>
                    <div class="num">${data["class"][i]["count"]}</div>
                </li>
            `
        }

        document.getElementById("allclass").innerHTML=`班級(顯示更少)`

        onclick(".classitem",function(element,event){
            domgetall(".current",function(element){
                element.classList.remove("current")
            })
            element.classList.add("current")
            main(element.dataset.id)
        })
    }else{
        document.getElementById("class").innerHTML=``
        document.getElementById("allclass").innerHTML=`班級(顯示更多)`
    }
})

document.getElementById("addClass").onclick=function(){
    document.getElementById("addClass").classList.add("current")
    lightbox(null,"dialog",function(){
        return`
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
    },null,false,"none")
    document.getElementById("close").onclick=function(){
        lightboxclose()

        document.getElementById("addClass").classList.remove("current")
    }
    document.getElementById("newclass").onsubmit=function(event){
        event.preventDefault()

        let classname=document.getElementById("classname").value

        if(classname){
            data["class"].push({
                name: classname,
                count: 0
            })
            updatedb(data)

            document.getElementById("newclass").reset()

            lightboxclose()

            document.getElementById("addClass").classList.remove("current")
        }else{
            document.getElementById("warning").innerHTML=`
                請輸入班級名稱
            `
        }
    }
}

let request=indexedDB.open("student",1)

request.onsuccess=function(event){
	dbdata=event.target.result

	getdb()
	main()

    document.getElementById("allstu").classList.add("current")
}

request.onupgradeneeded=function(event){
	dbdata=event.target.result

	let objectstore=dbdata.createObjectStore("data",{ keyPath: "id" })
    objectstore.add(data)
}

request.onerror=function(event){ console.error(event.target.errorCode) }

window.onbeforeunload=null