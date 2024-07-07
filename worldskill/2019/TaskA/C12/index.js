function arithmetic(value){
    value=value.replace(/(.+)\^(.+)/g,"$1**$2")

    return eval(value)
}

console.log(arithmetic("1+2*3"))
console.log(arithmetic("3^2%5"))
console.log(arithmetic("(-1-2)^3/9"))