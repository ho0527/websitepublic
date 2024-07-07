ajax("GET","api.php?getresponse=&id="+weblsget("51regionalresponseid"),function(event){
    let data=JSON.parse(event.responseText)

    if(data["success"]){
        let response=JSON.parse(data["data"][3])
        console.log("row=",response)
    }else{
        alert(data["data"])
    }
})