// ajax("GET",ajaxurl+"/getfoodlist",function(event,data){
//     if(data["success"]){
//         let row=data["data"]
//         for(let i=0;i<row.length;i=i+1){
//             docgetid("maintable").innerHTML=`
//                 ${docgetid("maintable").innerHTML}
//                 <tr class="textcenter">
//                     <td class="tabletd">${row[i]["name"]}</td>
//                     <td class="tabletd">${row[i]["price"]}</td>
//                     <td class="tabletd">
//                         <div class="input">
//                             <input type="number" class="ordercount textcenter" id="count_${i}" data-id="${row[i]["id"]}" data-name="${row[i]["name"]}" data-price="${row[i]["price"]}" value="0" min="0" step="1">
//                         </div>
//                     </td>
//                 </tr>
//             `
//         }
//     }else{
//         alert(data["data"])
//     }
// })

// onclick("#clear",function(element,event){
//     docgetall(".ordercount").forEach(function(event2){
//         event2.value="0"
//     })
// })

// onclick("#submit",function(element,event){
//     let orderdata=[]
//     let totalprice=0

//     lightbox(null,"lightbox",function(){
//         docgetall(".ordercount").forEach(function(event2){
//             if(event2.value!="0"){
//                 orderdata.push([event2.dataset.name,event2.dataset.price,event2.value])
//                 totalprice=totalprice+parseFloat(event2.dataset.price)*parseInt(event2.value)
//             }
//         })

//         return `
//             <div class="orderfoodlightbox">
//                 <div class="lightboxtitle textcenter">訂單確認</div>
//                 <div class="inputmargin">
//                     <div class="text">姓名</div>
//                     <div class="input light">
//                         <input type="text" id="username">
//                     </div>
//                 </div>
//                 <div class="orderfoodlightboxtable macossectiondivy">
//                     <table class="table light outborder">
//                         <tr class="textcenter">
//                             <th class="tableth">商品名稱</th>
//                             <th class="tableth">單價</th>
//                             <th class="tableth">數量</th>
//                         </tr>
//                         ${orderdata.map(function(event2){
//                             return `
//                                 <tr class="textcenter">
//                                     <td class="tabletd">${event2[0]}</td>
//                                     <td class="tabletd">${event2[1]}</td>
//                                     <td class="tabletd">
//                                         <div class="input">
//                                             <input type="number" class="ordercount" data-id="${event[0]}" value="${event2[2]}">
//                                         </div>
//                                     </td>
//                                 </tr>
//                             `
//                         }).join("")}
//                     </table>
//                 </div>
//                 <div class="text big textcenter">
//                     總價格: ${totalprice}
//                 </div>
//                 <div class="textcenter">
//                     <input type="button" class="button light" id="close" value="取消">
//                     <input type="button" class="button light" id="confirm" value="確認">
//                 </div>
//             </div>
//         `
//     },"close")

//     onclick("#confirm",function(element,event){
//         if(docgetid("username").value!=""){
//             ajax("POST",ajaxurl+"/newfoodorder",function(event,data){
//                 if(data["success"]){
//                     alert("上傳成功")
//                     location.reload()
//                 }else{
//                     alert(data["data"])
//                 }
//             },JSON.stringify({
//                 "username": docgetid("username").value,
//                 "orderdata": orderdata.map(function(event2){ return event2 }).join("，"),
//                 "totalprice": totalprice,
//             }))
//         }else{
//             alert("請輸入用戶名!")
//         }
//     })
// })


onclick(".image",function(element,event){
    lightbox(null,"lightbox",function(){
        return `
            <div class="position-relative">
                <div class="imagediv2">
                    <img src="${element.src}" class="image" id="bigimage" style="opacity: 1;" draggable="false">
                </div>
                <div class="textcenter">
                    <input type="button" class="button light" onclick="domgetid('bigimage').style.opacity=domgetid('bigimage').style.opacity-0.1" value="<">
                    <input type="button" class="button light" onclick="domgetid('bigimage').style.opacity=1" value="o">
                    <input type="button" class="button light" onclick="domgetid('bigimage').style.opacity=float(domgetid('bigimage').style.opacity)+0.1" value=">">
                </div>
                <input type="button" class="button light orderfoodclosebutton" id="close" value="X">
            </div>
        `
    },"close")
})