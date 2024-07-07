docgetid("submit").onclick=function(){
    let title=document.getElementById("title").value
    let description=document.getElementById("description").value
    let file=document.getElementById("file").files[0]
    let extendlist=[]

    docgetall("#extend>.opiniondiv").forEach(function(event){
        extendlist.push(event.dataset.id)
    })

    console.log(extendlist)

    ajax("POST","api/newopinion.php",function(event){
        let id=event.responseText
        alert("新增成功")
        location.href="opinion.php?id="+id
    },formdata([
        ["title",title],
        ["description",description],
        ["file",file],
        ["extend",extendlist.join("|&|")]
    ]),[])
}

divsort("opiniondiv",".sort")

startmacossection()