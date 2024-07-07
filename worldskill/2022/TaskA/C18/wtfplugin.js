setTimeout(function(){
    let image=document.querySelectorAll("img")
    let count
    for(let i=0;i<image.length;i=i+1){
        image[i].onclick=function(){
            count=i
            document.querySelectorAll("body")[0].innerHTML=document.querySelectorAll("body")[0].innerHTML+`
                <div id="lightbox">
                    <div id="mask"></div>
                    <div id="lightboxbody">
                        <div id="X">X</div>
                        <img src="${this.src}" id="wtfpluginmainimage" style="width: 100px">
                        <input type="button" id="left" value="<">
                        <input type="button" id="right" value=">">
                    </div>
                </div>
            `

            document.getElementById("left").onclick=function(){
                count=count-1
                document.getElementById("right").disabled=false
                if(count==0){ document.getElementById("left").disabled=true }
                document.getElementById("wtfpluginmainimage").src=document.querySelectorAll("img")[count].src
            }

            document.getElementById("right").onclick=function(){
                count=count+1
                document.getElementById("left").disabled=false
                if(count==image.length-1){ document.getElementById("right").disabled=true }
                document.getElementById("wtfpluginmainimage").src=document.querySelectorAll("img")[count].src
            }

            document.getElementById("X").onclick=function(){
                location.reload()
            }

            document.getElementById("mask").onclick=function(){
                location.reload()
            }

            document.getElementById("lightbox").style.position="fixed"
            document.getElementById("lightbox").style.top="0px"
            document.getElementById("lightbox").style.left="0px"
            document.getElementById("lightbox").style.bottom="0px"
            document.getElementById("lightbox").style.right="0px"
            document.getElementById("lightbox").style.zIndex="999"
            document.getElementById("lightbox").style.display="flex"
            document.getElementById("lightbox").style.justifyContent="center"
            document.getElementById("lightbox").style.alignItems="center"
            document.getElementById("lightbox").style.color="white"
            document.getElementById("lightbox").style.userSelect="none"

            document.getElementById("mask").style.position="fixed"
            document.getElementById("mask").style.top="0px"
            document.getElementById("mask").style.left="0px"
            document.getElementById("mask").style.bottom="0px"
            document.getElementById("mask").style.right="0px"
            document.getElementById("mask").style.height="100%"
            document.getElementById("mask").style.backgroundColor="rgba(41, 41, 41, 0.718)"

            document.getElementById("lightboxbody").style.position="absolute"
            document.getElementById("lightboxbody").style.left="50%"
            document.getElementById("lightboxbody").style.top="50%"
            document.getElementById("lightboxbody").style.transform="translate(-50%,-50%) scale(3)"
            document.getElementById("lightboxbody").style.textAlign="center"
            document.getElementById("lightboxbody").style.width="350px"
            document.getElementById("lightboxbody").style.border="1px rgb(45, 45, 45) solid"
            document.getElementById("lightboxbody").style.backgroundColor="rgba(100, 100, 100, 0.5)"
            document.getElementById("lightboxbody").style.borderRadius="15px"

            document.getElementById("wtfpluginmainimage").style.width="100px"

            document.getElementById("X").style.position="absolute"
            document.getElementById("X").style.top="-10px"
            document.getElementById("X").style.right="-10px"
            document.getElementById("X").style.cursor="pointer"

            document.getElementById("left").style.fontSize="15px"
            document.getElementById("left").style.width="50px"
            document.getElementById("left").style.height="100%"
            document.getElementById("left").style.position="absolute"
            document.getElementById("left").style.left="0px"
            document.getElementById("left").style.border="none"
            document.getElementById("left").style.backgroundColor="green"

            document.getElementById("right").style.fontSize="15px"
            document.getElementById("right").style.width="50px"
            document.getElementById("right").style.height="100%"
            document.getElementById("right").style.position="absolute"
            document.getElementById("right").style.right="0px"
            document.getElementById("right").style.border="none"
            document.getElementById("right").style.backgroundColor="green"
        }
    }
},100)