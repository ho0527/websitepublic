docgetid("useropinion").onclick=function(){
    docgetid("show").innerHTML=``
    docgetid("useropinion").classList.add("statisticselect")
    docgetid("useropinion").classList.remove("statisticnoneselect")
    docgetid("projectopinion").classList.remove("statisticselect")
    docgetid("projectopinion").classList.add("statisticnoneselect")
    docgetid("projectfacing").classList.remove("statisticselect")
    docgetid("projectfacing").classList.add("statisticnoneselect")
    oldajax("GET","api.php?statistic=useropinion").onload=function(){
        let data=JSON.parse(this.responseText)
        let userlist=[]
        let time=[]

        for(let i=0;i<data.length;i=i+1){
            userlist.push(data[i][0])
            time.push(data[i][1])
        }

        let div=doccreate("div")

        div.classList.add("statisticbartypea")
        // 創建一個長條圖
        Highcharts.chart(div,{
            chart:{
                type:"bar" // 指定圖表的類型
            },
            title:{
                text:"前三高意見數量" // 圖表的標題
            },
            xAxis:{
                title:{
                    text:"使用者id" // y軸的標題
                },
                categories:userlist // x軸的標籤
            },
            yAxis:{
                title:{
                    text:"數量" // y軸的標題
                }
            },
            series:[{
                name:"意見數量",
                data:time // 資料
            }]
        })

        docappendchild("show",div)
        for(let i=0;i<data.length;i=i+1){
            if(data[i][2]=="無資料"){
                let div=doccreate("div")

                div.classList.add("statisticbartypeb")
                div.classList.add("warning")
                div.innerHTML="userid:"+data[i][0]+" 無資料"
                docappendchild("show",div)
            }else{
                let div=doccreate("div")
                div.classList.add("statisticbartypeb")
                // 創建一個圓餅圖
                Highcharts.chart(div,{
                    chart:{
                        type:"pie" // 指定圖表的類型
                    },
                    title:{
                        text:"userid:"+data[i][0]+" 評分分布" // 圖表的標題
                    },
                    series:[{
                        name:"次數",
                        data:[ // 資料
                            { name:"1",y:data[i][2][1] },
                            { name:"2",y:data[i][2][2] },
                            { name:"3",y:data[i][2][3] },
                            { name:"4",y:data[i][2][4] },
                            { name:"5",y:data[i][2][5] }
                        ]
                    }]
                });

                docappendchild("show",div)
            }
        }
    }
}

docgetid("projectopinion").onclick=function(){
    docgetid("show").innerHTML=``
    docgetid("projectopinion").classList.add("statisticselect")
    docgetid("projectopinion").classList.remove("statisticnoneselect")
    docgetid("useropinion").classList.remove("statisticselect")
    docgetid("useropinion").classList.add("statisticnoneselect")
    docgetid("projectfacing").classList.remove("statisticselect")
    docgetid("projectfacing").classList.add("statisticnoneselect")
    oldajax("GET","api.php?statistic=projectopinion").onload=function(){
        let data=JSON.parse(this.responseText)
        let projetclist=[]
        let time=[]

        for(let i=0;i<data.length;i=i+1){
            projetclist.push(data[i][0])
            time.push(data[i][1])
        }
        let div=doccreate("div")

        div.classList.add("statisticbartypea")
        // 創建一個長條圖
        Highcharts.chart(div,{
            chart:{
                type:"bar" // 指定圖表的類型
            },
            title:{
                text:"每一個專案意見數量" // 圖表的標題
            },
            xAxis:{
                title:{
                    text:"使用者id" // y軸的標題
                },
                categories:projetclist // x軸的標籤
            },
            yAxis:{
                title:{
                    text:"數量" // y軸的標題
                }
            },
            series:[{
                name:"意見數量",
                data:time // 資料
            }]
        })

        docappendchild("show",div)
    }
}

docgetid("projectfacing").onclick=function(){
    docgetid("show").innerHTML=`
        <div class="grid">
            <div class="statisticleft">
                <table class="statisticlefttable" id="table">
                    <tr>
                        <td class="maintd">projectid</td>
                        <td class="maintd">function</td>
                    </tr>
                </table>
            </div>
            <div class="statisticright" id="showmain"></div>
        </div>
    `
    docgetid("projectfacing").classList.add("statisticselect")
    docgetid("projectfacing").classList.remove("statisticnoneselect")
    docgetid("useropinion").classList.remove("statisticselect")
    docgetid("useropinion").classList.add("statisticnoneselect")
    docgetid("projectopinion").classList.remove("statisticselect")
    docgetid("projectopinion").classList.add("statisticnoneselect")
    oldajax("GET","api.php?statistic=projectfacing").onload=function(){
        let data=JSON.parse(this.responseText)

        for(let i=0;i<data.length;i=i+1){
            let tr=doccreate("tr")
            tr.innerHTML=`
                <td class="maintd">${data[i][0]}</td>
                <td class="maintd"><input type="button" class="stbutton outline see" data-id="${i}" value="查看"></td>
            `
            docappendchild("table",tr)
        }

        docgetall(".see").forEach(function(event){
            event.onclick=function(){
                let dataid=this.dataset.id
                let maindata=[]
                let time=data[dataid][1]

                docgetid("showmain").innerHTML=``

                for(let i=0;i<data[dataid][2].length;i=i+1){
                    maindata.push({
                        name:data[dataid][2][i][0],
                        y:data[dataid][2][i][1]
                    })
                }

                let div=doccreate("div")

                div.classList.add("statisticbartext")
                div.innerHTML=`
                    意見總數量:${time}
                `
                docappendchild("showmain",div)

                let div2=doccreate("div")

                div2.classList.add("statisticbartypec")

                // 創建一個長條圖
                Highcharts.chart(div2,{
                    chart:{
                        type:"pie" // 指定圖表的類型
                    },
                    title:{
                        text:"每一個面項意見數量" // 圖表的標題
                    },
                    series:[{
                        name:"次數",
                        data:maindata // 資料
                    }]
                })
                docappendchild("showmain",div2)
            }
        })


    }
}

startmacossection()