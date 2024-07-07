<?php
    $db=new PDO("mysql:host=localhost;dbname=52jregional;charset=utf8","root","");

    function query($db,$query,$data=[]){
        $prepare=$db->prepare($query);
        $prepare->execute($data);
        return $prepare->fetchAll();
    }

    if(isset($_GET["submit"])){
        $firstname=$_GET["firstname"];
        $lastname=$_GET["lastname"];
        $phone=$_GET["phone"];
        $password=$_GET["password"];
        $verification=$_GET["verification"];
        $ans=$_GET["ans"];
        if($verification==$ans){
            query($db,"INSERT INTO `user`(`firstname`, `lastname`, `phone`, `password`)VALUES(?,?,?,?)",[$firstname,$lastname,$phone,$password]);
            ?><script>alert("新增成功");location.href="home.html"</script><?php
            session_unset();
        }else{
            ?><script>alert("驗證碼輸入錯誤");location.href="tickets.html"</script><?php
        }
    }else{
        header("location:tickets.html");
    }
?>