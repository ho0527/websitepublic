function removearrayduplicate(array){ // remove array duplicate  respone=>array
    let uniquearray=[];
    let seenelement={};
    for(let i=0;i<array.length;i=i+1){
        let element=array[i];
        if(!seenelement[element]){
            uniquearray.push(element);
            seenelement[element]=true;
        }
    }
    return uniquearray;
}