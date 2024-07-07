document.getElementById("submit").onclick=function(){
    let input=document.getElementById("input").value
    let maindata=""
    let r
    let g
    let b
    if(/^rgb\((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\,(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\,(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\)$/.test(input)){
        let data=input.split(",")
        console.log(data)
        console.log(parseInt(data[0]))
        r=parseInt(data[0].substring(4))
        g=parseInt(data[1])
        b=parseInt(data[2])
        r=r.toString(16)
        g=g.toString(16)
        b=b.toString(16)
        if(r.length == 1) r="0"+r
        if(g.length==1) g = "0" + g;
        if(b.length == 1) b = "0" + b
        maindata="the color type is: RGB<br>"+"HEX value: "+"#"+r+g+b+"<br>"+"RGB value: "+input
    }else if(/^\#(([0-9]|[a-f]|[A-F]){3}){1,2}$/.test(input)){
        if(input.length==4){
            r=parseInt("0"+input[1],16)
            g=parseInt("0"+input[2],16)
            b=parseInt("0"+input[3],16)
        }else{
            r=parseInt(input[1]+input[2],16)
            g=parseInt(input[3]+input[4],16)
            b=parseInt(input[5]+input[6],16)
        }
        maindata="the color type is: HEX<br>"+"HEX value: "+input+"<br>"+"RGB value: "+"rgb("+r+","+g+","+b+")"
    }else{
        maindata="ERROR"
    }
    document.getElementById("show").innerHTML=`
        Result<br>
        ${maindata}
    `
}


document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}