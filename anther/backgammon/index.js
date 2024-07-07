let chessboard=document.querySelector("#chessboard")
let cells=chessboard.querySelectorAll("td")
let grid=[]
let player=0// 0代表黑棋，1代表白棋

for(let i=0;i<6;i=i+1){
    grid[i]=[]
    for(let j=0;j<7;j=j+1){
        grid[i][j]=0
    }
}

for(let i=0;i<cells.length;i=i+1){
    cells[i].addEventListener("click",function(){
        if(this.style.backgroundColor=="black"||this.style.backgroundColor=="white"){
            console.log("FUCK 幹嘛按這格")
        }else{
            if(player==0){
                this.style.backgroundColor="black"
                player=1
            }else{
                this.style.backgroundColor="white"
                player=0
            }
        }
        check(player)
    })
}
function check(id){
    // Check rows
    for(let i=0;i<6;i=i+1){
        for(let j=0;j<4;j=j+1){
            if(grid[i][j]!=0&&[i][j]==grid[i][j+1]&&grid[i][j]==grid[i][j+2]&&grid[i][j]==grid[i][j+3]){
                win(id)
                break
            }
        }
    }

    // Check columns
    for(let i=0;i<3;i=i+1){
        for(let j=0;j<7;j=j+1){
            if(grid[i][j]!=0&&grid[i][j]==grid[i+1][j]&&grid[i][j]==grid[i+2][j]&&grid[i][j]==grid[i+3][j]){
                win(id)
                break
            }
        }
    }

    // Check diagonals(left to right)
    for(let i=0;i<3;i=i+1){
        for(let j=0;j<4;j=j+1){
            if(grid[i][j]!=0&&grid[i][j]==grid[i+1][j+1]&&grid[i][j]==grid[i+2][j+2]&&grid[i][j]==grid[i+3][j+3]){
                win(id)
                break
            }
        }
    }

    let total=0
    for(let i=0;i<cells.length;i=i+1){
        if(cells.style.backgroundColor=="black"||cells.style.backgroundColor=="white"){
            total=total+1
        }
    }
    if(total==cells.length){
        win("same")
    }else{
        console.log("countu")
    }
}



function win(id){
    if(id=="white"){
        mask.innerHTML=`
            <div class="div">
                <div class="mask"></div>
                <div class="body">
                    <h2 class="title">遊戲結束</h2>
                    <hr>
                    <h1>結果:black</h1>
                    <div class="buttonlist">
                        <button id="submit" name="enter" class="submit button">重新開始</button>
                    </div>
                </div>
            </div>
        `
    }else if(id=="black"){
        mask.innerHTML=`
            <div class="div">
                <div class="mask"></div>
                <div class="body">
                    <h2 class="title">遊戲結束</h2>
                    <hr>
                    <h1>結果:white</h1>
                    <div class="buttonlist">
                        <button id="submit" name="enter" class="submit button">重新開始</button>
                    </div>
                </div>
            </div>
        `
    }else{
        mask.innerHTML=`
            <div class="div">
                <div class="mask"></div>
                <div class="body">
                    <h2 class="title">遊戲結束</h2>
                    <hr>
                    <h1>結果:平手</h1>
                    <div class="buttonlist">
                        <button id="submit" name="enter" class="submit button">重新開始</button>
                    </div>
                </div>
            </div>
        `
    }
    document.getElementById("submit").onclick=function(){
        location.reload()
    }
}
