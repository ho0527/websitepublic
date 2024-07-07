// let checkPalindrome = string => {

// }

// /*TESTS FOR AVALIATIONS*/

// checkPalindrome('aabaa');
// checkPalindrome('abac');
// checkPalindrome('a');
// checkPalindrome('az');
// checkPalindrome('abacaba');
// checkPalindrome('z');
// checkPalindrome('aaabaaaa');
// checkPalindrome('zzzazzazz');


let x
let y
let offsetx=0
let offsety=0
document.getElementById("body").addEventListener("pointermove",function(event){
    event.preventDefault()
    x=event.clientX-offsetx
    y=event.clientY-offsety
    document.getElementById("square").style.top=(y-50)+"px"
    document.getElementById("square").style.left=(x-50)+"px"
})