<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>管理員專區</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <table>
            <tr>
                <td>
                    <h1>管理員專區<h1>
                    <input type="button" onclick="location.href='signup.php'" value="註冊">
                </td>
                <td class="search">
                    <input type="text" placeholder="查詢" name="search">
                    <input type="button" name="enter" value="送出">
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table">
                        <tr>
                            <td class="admin-num">編號</td>
                            <td class="admin">帳號</td>
                            <td class="admin">密碼</td>
                            <td class="admin">名字</td>
                            <td class="admin">權限</td>
                            <td class="admin">登入時間</td>
                            <td class="admin">登出時間</td>
                            <td class="admin">動作</td>
                            <td class="admin">動作時間</td>
                        </tr>
                        <?php
                            include("link.php");
                            if(isset($_GET["enter"])){
                                $text=$_GET["search"];
                                $data=mysqli_query($db,"SELECT*FROM `data` WHERE `number`LIKE'%$text%' or `username`LIKE'%$text%' or `password`LIKE'%$text%' or `name`LIKE'%$text%' or `permission`LIKE'%$text%' or `logintime`LIKE'%$text%' or `logouttime`LIKE'%$text%' or `move`LIKE'%$text%' or `movetime`LIKE'%$text%'");
                                $datatext=mysqli_fetch_row($data);
                                if($text==""){
                                    header("location:adminwelcome.php");
                                }
                                while($row=mysqli_fetch_assoc($data)){
                                    if($row["username"]=="admin"){
                                        ?>
                                        <tr>
                                            <td class="admin-num">
                                                <?= $row["number"]; ?>
                                                <input type="button"value="edit" disabled>
                                            </td>
                                            <td class="admin"><?= $row["username"]; ?></td>
                                            <td class="admin"><?= $row["password"]; ?></td>
                                            <td class="admin"><?= $row["name"]; ?></td>
                                            <td class="admin"><?= $row["permission"]; ?></td>
                                            <td class="admin"><?= $row["logintime"]; ?></td>
                                            <td class="admin"><?= $row["logouttime"]; ?></td>
                                            <td class="admin"><?= $row["move"]; ?></td>
                                            <td class="admin"><?= $row["movetime"]; ?></td>
                                        </tr>
                                        <?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td class="admin-num">
                                                <?= $row["number"]; ?>
                                                <input type="button" onclick="location.href='edit.php?val=<?= $row['number']; ?>'" value="edit">
                                            </td>
                                            <td class="admin"><?= $row["username"]; ?></td>
                                            <td class="admin"><?= $row["password"]; ?></td>
                                            <td class="admin"><?= $row["name"]; ?></td>
                                            <td class="admin"><?= $row["permission"]; ?></td>
                                            <td class="admin"><?= $row["logintime"]; ?></td>
                                            <td class="admin"><?= $row["logouttime"]; ?></td>
                                            <td class="admin"><?= $row["move"]; ?></td>
                                            <td class="admin"><?= $row["movetime"]; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }else{
                                $data=mysqli_query($db,"SELECT*FROM `data`");
                                while($row=mysqli_fetch_assoc($data)){
                                    if($row["username"]=="admin"){
                                        ?>
                                        <tr>
                                            <td class="admin-num">
                                                <?= $row["number"]; ?>
                                                <input type="button"value="edit" disabled>
                                            </td>
                                            <td class="admin"><?= $row["username"]; ?></td>
                                            <td class="admin"><?= $row["password"]; ?></td>
                                            <td class="admin"><?= $row["name"]; ?></td>
                                            <td class="admin"><?= $row["permission"]; ?></td>
                                            <td class="admin"><?= $row["logintime"]; ?></td>
                                            <td class="admin"><?= $row["logouttime"]; ?></td>
                                            <td class="admin"><?= $row["move"]; ?></td>
                                            <td class="admin"><?= $row["movetime"]; ?></td>
                                        </tr>
                                        <?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td class="admin-num">
                                                <?= $row["number"]; ?>
                                                <input type="button" onclick="location.href='edit.php?val=<?= $row['number']; ?>'" value="edit">
                                            </td>
                                            <td class="admin"><?= $row["username"]; ?></td>
                                            <td class="admin"><?= $row["password"]; ?></td>
                                            <td class="admin"><?= $row["name"]; ?></td>
                                            <td class="admin"><?= $row["permission"]; ?></td>
                                            <td class="admin"><?= $row["logintime"]; ?></td>
                                            <td class="admin"><?= $row["logouttime"]; ?></td>
                                            <td class="admin"><?= $row["move"]; ?></td>
                                            <td class="admin"><?= $row["movetime"]; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>