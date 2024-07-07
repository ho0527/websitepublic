docgetall(".viewbutton").forEach(function(event){
    event.onclick=function(){
        let ajax=oldajax("GET","api/viewplan.php?id="+event.dataset.id)

        ajax.onload=function(){
            let data=JSON.parse(ajax.responseText)
            console.log(data) // opinionrow,extend,userrow,[averagescore,count,usercheck]

            let score
            if(data[3][2]){
                score=`<input type="button" class="button" onclick="location.href='newscore.php?id=${data[0][2]}&opinionid=${data[0][0]}'" value="評價">`
            }else{
                score=`<input type="button" class="button disabled" value="已完成評價" disabled>`
            }

            let media=""

            if(data[0][7]=="audio"){
                media=`<div class="mediadiv"><audio class="media mediaaudiovideo" controls><source src="${data[0][6]}" type="audio/mpeg"></audio></div>`
            }else if(data[0][7]=="video"){
                media=`<div class="mediadiv"><video class="media mediaaudiovideo" controls><source src="${data[0][6]}" type="video/mp4"></video></div>`
            }else if(data[0][7]=="image"){
                media=`<div class="mediadiv"><img src="${data[0][6]}" class="media"></div>`
            }else{ }

            lightbox(null,"lightbox",function(){
                return `
                    <div class="opinion">
                        <div class="no">編號: ${data[0][9]}</div>
                        <div class="extend">引用: ${data[1]}</div>
                        <div class="postuser">發表者: ${data[2][1]}</div>
                        <div class="title">標題: ${data[0][4]}</div>
                        <div class="description">說明: ${data[0][5]}</div>
                        <div class="time">被評價的平均分數: ${data[3][0]}</div>
                        <div class="time">評價人數: ${data[3][1]}</div>
                        ${score}
                        ${media}
                        <div class="time">發布時間: ${data[0][8]}</div>
                    </div>
                `
            })
        }
    }
})