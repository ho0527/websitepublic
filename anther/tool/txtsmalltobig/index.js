document.getElementById("submit").onclick=function(){
    let file=document.getElementById("file").files[0]
    let reader=new FileReader()
    reader.onload=function(event){
        let data=event.target.result.split("\r\n")

        data.sort(function(a,b){ return a.toLowerCase().localeCompare(b.toLowerCase()) })

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