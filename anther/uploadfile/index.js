let files
let folderinput=document.getElementById("folderinput")
let fileinput=document.getElementById("fileinput")
let filelist=document.getElementById("filelist")
let input=[fileinput,folderinput]

input.forEach(function(event){
    event.addEventListener("change",function(addeventlistenerevent){
        files=addeventlistenerevent.target.files
        let fileList=document.getElementById('filelist')
        for(let i=0;i<files.length;i=i+1){
            let li=document.createElement('li')
            li.textContent=files[i].name
            li.classList.add("filelistli")
            fileList.appendChild(li)
        }
    })
})

document.getElementById("submit").onclick=function(){
    document.getElementById("main").style.display="none"
    document.getElementById("uploading").style.display="block"
    let xhr=new XMLHttpRequest()
    xhr.open("POST","upload.php",true)
    xhr.upload.addEventListener("progress",function(e){
        if(e.lengthComputable){
            let percent=(e.loaded/e.total)*100
            console.log(percent+"% uploaded")
            document.getElementById("progress").value=percent
            if(percent==100){
                document.getElementById("percent").innerHTML=`
                    已完成上傳!
                `
            }else{
                document.getElementById("percent").innerHTML=`
                    已完成: ${percent}%
                `
            }
        }
    },false)
    xhr.onreadystatechange=function(){
        if(xhr.readyState==4){
            if(xhr.status==200){
                let response=JSON.parse(xhr.responseText)
                console.log("response="+response)
                if(response.success){
                    Swal.fire({
                        title:"Upload Success",
                        icon:"success",
                        showConfirmButton:false,
                        timer:1500
                    }).then(function(){
                        document.getElementById("filelist").innerHTML=""
                        document.getElementById("progress").value=0
                        location.reload()
                    })
                }else{
                    Swal.fire({
                        title:"Upload Failed",
                        text:response.message,
                        icon:"error",
                        confirmButtonText:"OK"
                    }).then(function(){
                        document.getElementById("progress").value=0
                        document.getElementById("fileinput").disabled=false
                        document.getElementById("folderinput").disabled=false
                    })
                }
            }else{
                Swal.fire({
                    title:"Upload Failed",
                    text:"An error occurred while uploading the file",
                    icon:"error",
                    confirmButtonText:"OK"
                }).then(function(){
                    document.getElementById("progress").value=0
                    document.getElementById("fileinput").disabled=false
                    document.getElementById("folderinput").disabled=false
                })
            }
        }
    }
    let form=new FormData(document.getElementById("form"))
    for(let i=0;i<files.length;i=i+1){
        let newFileName=file(files[i],form.getAll("file[]"));
        form.set("file[]",files[i],newFileName)
    }
    xhr.send(form)
}

function file(file,files){
    let filename=file.name;
    let i=1;
    while(files.includes(filename)) {
        let [name,extension]=filename.split(".");
        filename=`${i}_${name}.${extension}`;
        i=i+1;
    }
    return filename;
}

document.getElementById("reflashbutton").onclick=function(){
    location.href="index.html"
}
