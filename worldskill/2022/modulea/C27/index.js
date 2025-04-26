oldajax("GET","data.json").onload=function(){
    let data=JSON.parse(this.responseText);

    let commentcount=0
    let starcount=0

    for(let i=0;i<data.length;i=i+1){
        if(data[i]["published"]){
            let ratingdata=`<img src="star.svg" alt="star" class="w-4">`
            let certifieddata=""

            for(let j=1;j<data[i]["rating"];j=j+1){
                ratingdata=ratingdata+`<img src="star.svg" alt="star" class="w-4 ml-1">`
            }


            if(data[i]["certified"]){
                certifieddata=`
                <span class="certified-badge">
                    <img src="certified.svg" alt="certified" class="w-4">
                </span>
                `
            }

            let div=doccreate("div")
            div.classList.add("user-review-container")
            div.classList.add("mt-0")
            div.innerHTML=`
                <article>
                    <h3>
                        ${data[i]["author"]}
                        ${certifieddata}
                    </h3>
                    <div class="stars">${ratingdata}</div>
                    <p>${data[i]["content"]}</p>
                </article>
            `
            docappendchild("reviews",div)

            commentcount=commentcount+1
            starcount=starcount+data[i]["rating"]
        }
    }

    docgetid("average").innerHTML=Math.round((starcount/commentcount)*10)/10
}