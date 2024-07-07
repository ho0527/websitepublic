let row=json(weblsget("54national_modulee_all"))??[]
console.log(row)
let maininnerhtml=``

for(let i=0;i<row.length;i=i+1){
    let orderdata=row[i]["orderlist"]
    let orderdatadata=[]
    let totalprice=0

    for(let j=0;j<orderdata.length;j=j+1){
        orderdatadata.push(orderdata[j]["name"]+"*"+orderdata[j]["num"])
        totalprice=totalprice+orderdata[j]["price"]*orderdata[j]["num"]
    }

    maininnerhtml=`
        ${maininnerhtml}
        <tr class="textcenter">
            <td class="tabletd" rowspan="${orderdatadata.length}">${row[i]["inorout"]=="in"?"內用":"外帶"} - ${row[i]["count"]}人</td>
            <td class="tabletd">${orderdatadata[0]}</td>
            <td class="tabletd" rowspan="${orderdatadata.length}">${totalprice}</td>
            <td class="tabletd" rowspan="${orderdatadata.length}">
                <input type="button" class="button warn deletebutton" data-id="${i}" value="刪除">
            </td>
        </tr>
    `

    for(let j=1;j<orderdatadata.length;j=j+1){
        maininnerhtml=`
            ${maininnerhtml}
            <tr class="textcenter">
                <td class="tabletd">${orderdatadata[j]}</td>
            </tr>
        `
    }
}

docgetid("maintable").innerHTML=`
    <tr class="textcenter">
        <th class="adminttabletd">基本資料</th>
        <th class="adminttabletd">購買內容</th>
        <th class="adminttabletd">總價格</th>
        <th class="adminttabletd">功能區</th>
    </tr>
    ${maininnerhtml}
`

onclick(".deletebutton",function(element,event){
    if(confirm("確定要刪除嗎?")){
        row.splice(dataset(element,"id"),1)
        weblsset("54national_modulee_all",str(row))
        alert("刪除成功")
        location.reload()
    }
})