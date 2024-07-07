if(document.getElementById("file")){
    document.getElementById("file").onchange=function(event){
        let file=event.target.files[0]
        let filereader=new FileReader()
        filereader.onload=function(){
            document.getElementById("image").value=filereader.result
        }
        filereader.readAsDataURL(file)
    }
}