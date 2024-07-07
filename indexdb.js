function dbinsert(dbname,insertdata){ // 資料庫名 要輸入的值(使用json)
    let transaction=db.transaction([dbname],"readwrite")
    let objectStore=transaction.objectStore(dbname)
    let request=objectStore.add(insertdata)
    request.onsuccess=function(){
        console.log(dbname+" added to the database success")
    }

}

function dbupdate(dbname,updatefield,updatedata,updatevalue){ // 資料庫名 要改的欄位(int) 要更新的欄位 改成的值
    let transaction=db.transaction([dbname],"readwrite")
    let objectStore=transaction.objectStore(dbname)
    let request=objectStore.get(updatefield)
    request.onsuccess=function(event){
        let result=event.target.result
        result[updatedata]=updatevalue
        let updaterequest=objectStore.put(result)
        updaterequest.onsuccess=function(){
            console.log(dbname+" update success")
        }
    }

}

function dbdelete(dbname,deletedata){ // 資料庫名 刪除的欄位(int)
    let transaction=db.transaction([dbname],"readwrite")
    let objectStore=transaction.objectStore(dbname)
    let request=objectStore.delete(deletedata)
    request.onsuccess=function(){
        console.log(dbname+" delete success")
    }
}

function dbselect(dbname,selectfield,selectvalue,callback){ // 資料庫名 要查詢的欄位(int) 要找的值 回傳函式
    let transaction=db.transaction([dbname],"readwrite")
    let objectStore=transaction.objectStore(dbname)
    let index=objectStore.index(selectfield)
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
}
