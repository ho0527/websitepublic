let ans=""
let presscount=0

for(let i=0;i<document.querySelectorAll(".digicode-button").length;i=i+1){
    let key
    document.querySelectorAll(".digicode-button")[i].onclick=function(){
        document.getElementById("audio").src="beep.mp3"
        document.getElementById("audio").volume=1
        document.getElementById("audio").load()
        document.getElementById("audio").play()

        key=i+1
        console.log("key="+key)
        if(key==11){
            key="0"
        }
        ans=ans+key
        presscount=presscount+1
        if(presscount==4){
            if(ans=="6789"){
                document.querySelectorAll(".digicode-button")[9].classList.add("on")
                document.getElementById("audio").src="success.mp3"
                document.getElementById("audio").volume=1
                document.getElementById("audio").load()
                document.getElementById("audio").play()
                setTimeout(function(){
                    location.reload()
                },10000);
            }else{
                ans=""
                presscount=0
            }
        }
    }
}