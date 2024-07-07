function factor(num){
    if(Number.isInteger(num)){
        let factor=[]
        num=Math.abs(num)
        for(let i=1;i<=num;i++){
            if(num%i==0){
                factor.push(i)
            }
        }
        return factor
    }else{
        return "[WARNING]function variable type error"
    }
}

function commonfactor(num1,num2){
    let ans=[]
    for(let i=0;i<num1.length;i++){
        for(let j=0;j<num2.length;j++){
            if(num1[i]==num2[j]){
                ans.push(num1[i])
                break
            }
        }
    }
    return ans
}

function calculatefraction(up1,down1,operand,up2,down2){
    let up
    let down
    let lowerthan0
    if(operand=="+"){
        up=up1*down2+up2*down1
        down=down1*down2
    }else if(operand=="-"){
        up=up1*down2-up2*down1
        down=down1*down2
        if(up<=0){
            lowerthan0="true"
        }
    }else if(operand=="*"||operand==""){
        up=up1*up2
        down=down1*down2
    }else if(operand=="/"||operand==""){
        up=up1*down2
        down=up2*down1
    }else{
        ans="[WARNING]function operand type error"
    }

    let commonfactorreturn=commonfactor(factor(Math.abs(up)),factor(Math.abs(down)))
    return (up/commonfactorreturn[commonfactorreturn.length-1])+"/"+(down/commonfactorreturn[commonfactorreturn.length-1])
}

console.log(calculatefraction(5,3,"+",5,6))