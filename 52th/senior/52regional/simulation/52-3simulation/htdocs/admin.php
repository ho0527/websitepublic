<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>網站前台登入介面</title>
    <link rel="stylesheet" href="index.css">
</head>
    <body>
      <table>
         <tr>
            <td>
               <form>
                  <h1>
                     管理者專區
                     <button type="button" onclick="location.href='signup.php'">新增</button>
                     <input type="submit" name="logout" value="登出">
                  </h1>
               </form>
            </td>
            <td class="adminfind">
               <form>
                  <input type="search" name="search" placeholder="查詢">
                  <input type="submit" name="enter" value="送出">
               </form>
            </td>
         </tr>
         <tr>
            <td style="width:90%;">
               <table class="adminmiantable">
                  <form>
                     <tr>
                        <td class="admintablenum">編號</td>
                        <td class="admintable">使用者帳號</td>
                        <td class="admintable">密碼</td>
                        <td class="admintable">名稱</td>
                        <td class="admintable">權限</td>
                        <td class="admintable">登入時間</td>
                        <td class="admintable">登出時間</td>
                        <td class="admintable">動作</td>
                        <td class="admintable">動作時間</td>
                     </tr>
                     <?php
                        include("link.php");
                        include("admindef.php");
                        if(isset($_GET["logout"])){
                             session_unset();
                           ?><script>alert("登出成功!");location.href="index.php"</script><?php
                        }
                        if(isset($_GET["enter"])){
                           $_SESSION["type"]=$_GET["search"];
                           header("location:admin.php");
                        }
                        if(isset($_SESSION["type"])){
                           $type=$_SESSION["type"];
                           if($type==""){
                              unset($_SESSION["type"]);
                              header("location:admin.php");
                           }else{
                              $data=mysqli_query($db,"SELECT*FROM `data` WHERE `usernumber`LIKE'%$type%'or`username`LIKE'%$type%'or`password`LIKE'%$type%'or`name`LIKE'%$type%'or`permission`LIKE'%$type%'or`logintime`LIKE'%$type%'or`logouttime`LIKE'%$type%'or`move`LIKE'%$type%'or`movetime`LIKE'%$type%'");

                              up($data,1);
                           }
                        }else{
                           $data=mysqli_query($db,"SELECT*FROM `data`");
                           up($data,1);
                        }
                        if(isset($_GET["del"])){
                           $number=$_GET["del"];
                           $user=mysqli_query($db,"SELECT*FROM `user` WHERE `number`='$number'");
                           $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `number`='$number'");
                           if($row=mysqli_fetch_row($user)){
                              mysqli_query($db,"DELETE FROM `user` WHERE `number`='$number'");
                              mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$number','$row[1]','$row[2]','$row[3]','一般使用者','','','管理員刪除','$time')");
                              ?><script>alert("刪除成功!");location.href="admin.php"</script><?php
                           }elseif($row=mysqli_fetch_row($admin)){
                              mysqli_query($db,"DELETE FROM `admin` WHERE `number`='$number'");
                              mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$number','$row[1]','$row[2]','$row[3]','管理者','','','管理員刪除','$time')");
                              ?><script>alert("刪除成功!");location.href="admin.php"</script><?php
                           }else{
                              ?><script>alert("帳號已被刪除!");location.href="admin.php"</script><?php
                           }
                        }
                     ?>
                  </form>
               </table>
            </td>
         </tr>
      </table>
    </body>
</html>