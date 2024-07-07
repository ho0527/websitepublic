rangeslider("slider",{
    "min": 0,
    "max": 1000
},50,[400,600],true,true,true,true,"roundstyle","$",function(value){
    innerhtml("#left",value[0],false)
    innerhtml("#right",value[1],false)
})