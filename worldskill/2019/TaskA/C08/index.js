ajax("GET","taglist.json",function(event){
    taglist=JSON.parse(event.responseText)
    tag("tagdiv",taglist)
})