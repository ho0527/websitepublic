<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>資料視覺化</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/macossection.css">
        <script src="plugin/js/macossection.js"></script>
    </head>
    <body>
        <div class="grid">
            <div class="left">
                <input type="button" onclick="location.href='index.html'" value="Leave Chat">
                <h1>test1</h1>
                <div class="div">使用者列表</div>
                user1<br>
                user2<br>
                user3<br>
                user4<br>
                user5<br>
                user6<br>
                user7<br>
            </div>
            <div class="right macossectiondiv" id="main">
            </div>
            <div class="rightright">test1 Hi</div>
            <div class="newchat">
                <form method="POST">
                    <input type="text" class="input" name="input">
                    <input type="submit" class="submit" name="submit">
                </form>
            </div>
        </div>
        <?php
            include("link.php");
            if(isset($_POST["submit"])){
                $input=$_POST["input"];
                query($db,"INSERT INTO `log`(`username`,`context`)VALUES('test1',?)",[$input]);
                ?><script>location.href="login.php"</script><?php
            }
        ?>
    </body>
    <script>
        function main(){
            document.getElementById("main").innerHTML=``

            let ajax=new XMLHttpRequest()
            ajax.onload=function(){
                let data=JSON.parse(ajax.responseText)

                for(let i=0;i<data.length;i=i+1){
                    document.getElementById("main").innerHTML=`
                        ${document.getElementById("main").innerHTML}
                        <h2>${data[i][1]}</h2>
                        <p>${data[i][2]}</p>
                    `
                }
            }
            ajax.open("GET","api.php")
            ajax.send()
        }
        main()
        setInterval(function(){
            main()
        },1000)
    </script>
</html>