docgetid("maintitle").innerHTML=weblsget("tutorialresponsemaintitle")
docgetid("link").value=weblsget("tutorialresponselink")

docgetid("goback").onclick=function(){
    let link=weblsget("tutorialresponselink")
    weblsset("tutorialresponsemaintitle",null)
    weblsset("tutorialresponselink",null)
    location.href="../"+link
}

docgetid("submit").onclick=function(){
    ajax("POST","/backend/tutorial/response",function(event){
        let data=JSON.parse(event.responseText)
        if(data["success"]){
            alert("感謝您的填寫,請等待查核,有進度後會發送通知給您(如果有填寫聯絡方式的話)。")
            let link=weblsget("tutorialresponselink")
            weblsset("tutorialresponsemaintitle",null)
            weblsset("tutorialresponselink",null)
            location.href="../"+link
        }else{
            alert(data["data"])
        }
    },JSON.stringify({
        "link": docgetid("link").value,
        "subject": docgetid("subject").value,
        "id": docgetid("id").value,
        "title": docgetid("title").value,
        "description": docgetid("description").value,
        "connecttype": docgetid("connecttype").value,
        "connectdata": docgetid("connectdata").value
    }))
    lightbox(null,"lightbox",function(){
        return `上傳中請稍等.....`
    },null,false,"none")
}