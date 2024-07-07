function change(key){
    let firstcolor=""
    let lastcolor=""
    let face=""
    if(key=="1"){
        firstcolor="#83B458"
        lastcolor="#A9DB7A"
        face="happy"
    }else if(key=="2"){
        firstcolor="#DAA656"
        lastcolor="#EED16B"
        face="normal"
    }else{
        firstcolor="#DE5D63"
        lastcolor="#EE9295"
        face="sad"
    }
    document.querySelectorAll(".mouth")[0].classList.remove(document.querySelectorAll(".mouth")[0].classList[1])
    document.querySelectorAll(".mouth")[0].classList.add(face)
    document.querySelectorAll(".eye")[0].style.background=firstcolor
    document.querySelectorAll(".eye")[1].style.background=firstcolor
    document.querySelectorAll(".face")[0].style.borderColor=firstcolor
    document.querySelectorAll(".face")[0].style.background=lastcolor
}