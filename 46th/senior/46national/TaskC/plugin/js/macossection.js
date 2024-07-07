/*
    標題: macos滑動滾輪 js
    參考: IG: this.web
    作者: 小賀chris
    製作及log:
    2023/05/11 21:35:30 BATA 1.0.0
    2023/05/12 18:57:56 BATA 2.0.0
    2023/06/26 18:24:28 BATA 2.1.2 // 新增xy及全軸功能

        |-------    -----    -                     -     -----  -----  -----   -------|
       |-------    -        -            - - -          -                     -------|
      |-------    -        -------    -          -     -----    --       --  -------|
     |-------    -        -     -    -          -         -      --     --  -------|
    |-------    -----    -     -    -          -     -----         -----  -------|
*/

let macostimer

setTimeout(function(){
    document.querySelectorAll(".macossectiondiv").forEach(function(event){
        event.addEventListener("scroll",function(){
            clearTimeout(macostimer)
            event.setAttribute("scroll","true")
            macostimer=setTimeout(function(){
                event.removeAttribute("scroll")
            },500)
        })
    })
    document.querySelectorAll(".macossectiondivx").forEach(function(event){
        event.addEventListener("scroll",function(){
            clearTimeout(macostimer)
            event.setAttribute("scroll","true")
            macostimer=setTimeout(function(){
                event.removeAttribute("scroll")
            },500)
        })
    })
    document.querySelectorAll(".macossectiondivall").forEach(function(event){
        event.addEventListener("scroll",function(){
            clearTimeout(macostimer)
            event.setAttribute("scroll","true")
            macostimer=setTimeout(function(){
                event.removeAttribute("scroll")
            },500)
        })
    })
},200)