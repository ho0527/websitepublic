let sortPositive = arr => {
	let data=[]
	for(let i=0;i<=arr.length;i=i+1){
		if(/^[0-9]+$/.test(arr[i])){
			data.push(arr[i])
		}
	}
	console.log(data)
	// put your code here
}

/*TESTS FOR AVALIATIONS*/

sortPositive([-2, 150, 190, 170, -3, -4, 160, 180]);
sortPositive([-1, -1, -1, -1, -1]);
sortPositive([-1]);
sortPositive([4, 2, 9, 11, 2, 16]);
sortPositive([2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 1]);
sortPositive([23, 54, -1, 43, 1, -1, -1, 77, -1, -1, -1, 3]);