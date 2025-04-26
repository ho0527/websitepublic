/**
 * Deep Clone
 */

function deepClone(data){
    if(Array.isArray(data)){ // 判斷是不是array
        return data.map(function(event){ return deepClone(event) }) // 將每個元素用deep clone回傳
    }else if(typeof(data)=="object"&&data!=null){  // 判斷是不是object(因為null也是object的一部分所以要再加判斷)
        // deep clone main function
        let clone={}
        for(let i=0;i<Object.keys(data).length;i=i+1){
            let key=Object.keys(data)[i];
            clone[key]=deepClone(data[key]);
        }
        return clone
    }else{
        return data // 如果都不是就直接回傳data
    }
}