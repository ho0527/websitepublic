/*
    標題: canva簡化
    作者: 小賀chris
    製作及log:
    2023/05/13  19:12:32 BATA 0.0.1 創建基本function

        |-------    -----    -                     -     -----     -------|
       |-------    -        -            - - -          -         -------|
      |-------    -        -------    -          -     -----     -------|
     |-------    -        -     -    -          -         -     -------|
    |-------    -----    -     -    -          -     -----     -------|
*/

function canva(name){ return document.getElementById(name) }

function getcontext(name,value){ return name.getContext(value) }

function createcanva(name,width,height){
    document.getElementById(name).width=width
    document.getElementById(name).height=height
}

function drawline(name,startx,starty,endx,endy,width){
    let canvas=document.getElementById("name")
}