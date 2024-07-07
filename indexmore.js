function statutest(statu){
    if(statu=="SUCCESS"){
        return "green"
    }else if(statu=="FAILED"){
        return "red"
    }else if(statu=="N/A"||statu=="subjective"){
        return "gray"
    }else{
        return "white"
    }
}

function linktest(link,title){
    if(link["link"]!=""&&link["link"]!=undefined&&link["statu"]!="N/A"&&link["statu"]!="subjective"){
        return "<a href='"+link["link"]+"' class='a'>"+title+"</a>"
    }else{
        return title
    }
}

oldajax("GET","list.json").onload=function(){
    let data=JSON.parse(this.responseText)["data"]
    for(let i=0;i<data.length;i=i+1){
        let title=data[i]["title"]
        let list=data[i]["data"]
        let listdata=""
        if(data[i]["ps"]=="list"){
            for(let j=0;j<list.length;j=j+1){
                let listdata2=""
                for(let k=0;k<list[j]["data"].length;k=k+1){
                    let questiondata=""
                    let question=list[j]["data"][k]["data"]

                    for(let l=0;l<question.length;l=l+1){
                        let descriptiondata=""
                        for(let m=0;m<question[l]["data"].length;m=m+1){
                            descriptiondata=descriptiondata+`
                                <div class="descriptiondiv">
                                    <div class="no">-</div>
                                    <div class="description">${question[l]["data"][m]["description"]}</div>
                                    <div class="dot">.....................</div>
                                    <div class="statu" style="color: ${statutest(question[l]["data"][m]["statu"])}">${question[l]["data"][m]["statu"]}</div>
                                </div>
                            `
                        }
                        questiondata=questiondata+`
                            <div class="questiondiv">
                                <div class="no">no.${question[l]["no"]}</div>
                                <div class="description">${linktest(question[l],question[l]["description"])}</div>
                                <div class="dot">.....................</div>
                                <div class="statu" style="color: ${statutest(question[l]["statu"])}">${question[l]["statu"]}</div>
                            </div>
                            <div class="list3">${descriptiondata}</div>
                        `
                    }

                    listdata2=listdata2+`
                        <div class="listdiv">
                            <div class="title">項目: ${list[j]["data"][k]["list"]}-${list[j]["data"][k]["description"]}</div>
                            <div class="dot">.....................</div>
                            <div class="statu" style="color: ${statutest(list[j]["data"][k]["statu"])}">${list[j]["data"][k]["statu"]}</div>
                        </div>
                        <div class="list2">${questiondata}</div>
                    `
                }

                listdata=listdata+`
                    <div class="listdiv">
                        <div class="title">${linktest(list[j],list[j]["title"])}</div>
                        <div class="dot">.....................</div>
                        <div class="statu" style="color: ${statutest(list[j]["statu"])}">${list[j]["statu"]}</div>
                        <div class="cdate">createdate: ${list[j]["createdate"]}</div>
                        <div class="udate">updatedate: ${list[j]["updatedate"]}</div>
                    </div>
                    <div class="list2">${listdata2}</div>
                `
            }
        }else if(data[i]["ps"]=="question"){
            for(let j=0;j<list.length;j=j+1){
                let questiondata=""

                for(let k=0;k<list[j]["data"].length;k=k+1){
                    let question=list[j]["data"]
                    let descriptiondata=""

                    for(let l=0;l<question[k]["data"].length;l=l+1){
                        descriptiondata=descriptiondata+`
                            <div class="descriptiondiv">
                                <div class="no">-</div>
                                <div class="description">${question[k]["description"]}</div>
                                <div class="dot">.....................</div>
                                <div class="statu" style="color: ${statutest(question[k]["statu"])}">${question[k]["statu"]}</div>
                            </div>
                        `
                    }

                    questiondata=questiondata+`
                        <div class="questiondiv">
                            <div class="no">no.${question[k]["no"]}</div>
                            <div class="description">${question[k]["description"]}</div>
                            <div class="dot">.....................</div>
                            <div class="statu" style="color: ${statutest(question[k]["statu"])}">${question[k]["statu"]}</div>
                        </div>
                        <div class="list3">${descriptiondata}</div>
                    `
                }

                listdata=listdata+`
                    <div class="listdiv">
                        <div class="title">項目: ${list[j]["list"]}-${list[j]["description"]}</div>
                        <div class="dot">.....................</div>
                        <div class="statu" style="color: ${statutest(list[j]["statu"])}">${list[j]["statu"]}</div>
                    </div>
                    <div class="list2">${questiondata}</div>
                `
            }
        }

        document.getElementById("body").innerHTML=document.getElementById("body").innerHTML+`
            <div class="subjectdiv">
                <div class="subject">
                    <div class="title">${linktest(data[i],title)}</div>
                    .......
                    <div class="statu" style="color: ${statutest(data[i]["statu"])}">${data[i]["statu"]}</div>
                    <div class="cdate">createdate: ${data[i]["createdate"]}</div>
                    <div class="udate">updatedate: ${data[i]["updatedate"]}</div>
                </div>
                <div class="list1">${listdata}</div>
            <div>
        `
    }
}

oldajax("GET","listmore.json").onload=function(){
    let data=JSON.parse(this.responseText)["data"]
    for(let i=0;i<data.length;i=i+1){
        let title=data[i]["title"]
        let list=data[i]["data"]
        let listdata=""
        if(data[i]["ps"]=="list"){
            for(let j=0;j<list.length;j=j+1){
                let listdata2=""
                for(let k=0;k<list[j]["data"].length;k=k+1){
                    let questiondata=""
                    let question=list[j]["data"][k]["data"]

                    for(let l=0;l<question.length;l=l+1){
                        let descriptiondata=""
                        for(let m=0;m<question[l]["data"].length;m=m+1){
                            descriptiondata=descriptiondata+`
                                <div class="descriptiondiv">
                                    <div class="no">-</div>
                                    <div class="description">${question[l]["data"][m]["description"]}</div>
                                    <div class="dot">.....................</div>
                                    <div class="statu" style="color: ${statutest(question[l]["data"][m]["statu"])}">${question[l]["data"][m]["statu"]}</div>
                                </div>
                            `
                        }
                        questiondata=questiondata+`
                            <div class="questiondiv">
                                <div class="no">no.${question[l]["no"]}</div>
                                <div class="description">${linktest(question[l],question[l]["description"])}</div>
                                <div class="dot">.....................</div>
                                <div class="statu" style="color: ${statutest(question[l]["statu"])}">${question[l]["statu"]}</div>
                            </div>
                            <div class="list3">${descriptiondata}</div>
                        `
                    }

                    listdata2=listdata2+`
                        <div class="listdiv">
                            <div class="title">項目: ${list[j]["data"][k]["list"]}-${list[j]["data"][k]["description"]}</div>
                            <div class="dot">.....................</div>
                            <div class="statu" style="color: ${statutest(list[j]["data"][k]["statu"])}">${list[j]["data"][k]["statu"]}</div>
                        </div>
                        <div class="list2">${questiondata}</div>
                    `
                }

                listdata=listdata+`
                    <div class="listdiv">
                        <div class="title">${linktest(list[j],list[j]["title"])}</div>
                        <div class="dot">.....................</div>
                        <div class="statu" style="color: ${statutest(list[j]["statu"])}">${list[j]["statu"]}</div>
                        <div class="cdate">createdate: ${list[j]["createdate"]}</div>
                        <div class="udate">updatedate: ${list[j]["updatedate"]}</div>
                    </div>
                    <div class="list2">${listdata2}</div>
                `
            }
        }else if(data[i]["ps"]=="question"){
            for(let j=0;j<list.length;j=j+1){
                let questiondata=""

                for(let k=0;k<list[j]["data"].length;k=k+1){
                    let question=list[j]["data"]
                    let descriptiondata=""

                    for(let l=0;l<question[k]["data"].length;l=l+1){
                        descriptiondata=descriptiondata+`
                            <div class="descriptiondiv">
                                <div class="no">-</div>
                                <div class="description">${question[k]["description"]}</div>
                                <div class="dot">.....................</div>
                                <div class="statu" style="color: ${statutest(question[k]["statu"])}">${question[k]["statu"]}</div>
                            </div>
                        `
                    }

                    questiondata=questiondata+`
                        <div class="questiondiv">
                            <div class="no">no.${question[k]["no"]}</div>
                            <div class="description">${question[k]["description"]}</div>
                            <div class="dot">.....................</div>
                            <div class="statu" style="color: ${statutest(question[k]["statu"])}">${question[k]["statu"]}</div>
                        </div>
                        <div class="list3">${descriptiondata}</div>
                    `
                }

                listdata=listdata+`
                    <div class="listdiv">
                        <div class="title">項目: ${list[j]["list"]}-${list[j]["description"]}</div>
                        <div class="dot">.....................</div>
                        <div class="statu" style="color: ${statutest(list[j]["statu"])}">${list[j]["statu"]}</div>
                    </div>
                    <div class="list2">${questiondata}</div>
                `
            }
        }

        document.getElementById("body").innerHTML=document.getElementById("body").innerHTML+`
            <div class="subjectdiv">
                <div class="subject">
                    <div class="title">${linktest(data[i],title)}</div>
                    .......
                    <div class="statu" style="color: ${statutest(data[i]["statu"])}">${data[i]["statu"]}</div>
                    <div class="cdate">createdate: ${data[i]["createdate"]}</div>
                    <div class="udate">updatedate: ${data[i]["updatedate"]}</div>
                </div>
                <div class="list1">${listdata}</div>
            <div>
        `
    }
}

startmacossection()