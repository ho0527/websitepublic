document.querySelectorAll(".card").forEach(function(card){
    let isdrag=false
    let x
    let y
    let offsetx=0
    let offsety=0

    card.addEventListener("pointerdown",function(event){
        isdrag=true
        x=event.clientX-offsetx
        y=event.clientY-offsety
        card.style.transform="rotate(15deg)"
    })

    document.addEventListener("pointerup",function(){
        if(isdrag){
            isdrag=false
            card.style.transform="rotate(0deg)"
        }
    })
})
document.querySelectorAll(".card").forEach(function(card) {
    let isdrag = false;
    let x;
    let y;
    let offsetx = 0;
    let offsety = 0;

    card.addEventListener("pointerdown", function(event) {
        isdrag = true;
        x = event.clientX - offsetx;
        y = event.clientY - offsety;
        card.style.transform = "rotate(15deg)";
    });

    document.addEventListener("pointerup", function() {
        if (isdrag) {
            isdrag = false;
            card.style.transform = "rotate(0deg)";
        }
    });
});

$(function() {
    $("#sortable1").sortable({
        connectWith: ".group-sortable"
    }).disableSelection();

    $("#sortable2").sortable({
        connectWith: ".group-sortable"
    }).disableSelection();

    $("#sortable3").sortable({
        connectWith: ".group-sortable"
    }).disableSelection();
});
