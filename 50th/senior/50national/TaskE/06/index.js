document.getElementById("submit").onclick=function(){
    let file=document.getElementById("file").files[0]
    let reader=new FileReader()
    reader.onload=function(event){
        let data=[]
        let line=event.target.result.split("\r\n")
        console.log(line)

        for (let i=0;i<line.length;i=i+1){
            if(data.indexOf(line[i])==-1){
                data.push(line[i])
            }
        }
        let a=document.createElement("a")
        let blob=new Blob([data.join("\n")],{ type:"text/plain" })
        let url=URL.createObjectURL(blob)
        a.classList.add("a")
        a.href=url
        a.download=file.name
        a.innerHTML=`${file.name}`
        document.getElementById("output").appendChild(a)
    }
    reader.readAsText(file)
}