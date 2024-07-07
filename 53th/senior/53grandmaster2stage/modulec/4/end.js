function end(){
    let src="material/picture/failed.svg"

    docgetid("game").style.display="none"
    docgetid("end").style.display="block"

    if(success){
        src="material/picture/success.svg"
    }

    docgetid("end").innerHTML=`
        <img src="${src}" class="gameendimage" draggable="false">
        <h4 class="endnickname">${weblsget("53grandmaster2stagemodulecnickname")}</h4>
        <h4 class="endscore">${totalscore}</h4>
        <h4 class="endtime">${String(parseInt(totaltime/60)).padStart(2,"0")}:${String(parseInt(totaltime%60)).padStart(2,"0")}</h4>
        <input type="button" class="button restartbutton" id="restart" value="重新開始">
    `

    docgetid("restart").onclick=function(){
        weblsset("53grandmaster2stagemodulecnickname",null)
        location.reload()
    }
}