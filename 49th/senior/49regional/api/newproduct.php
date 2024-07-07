<?php
    include("../link.php");
    if($_POST["submit"]){
        $button=$_POST["button"];
        $description=$_POST["description"];
        $date=$_POST["date"];
        $link=$_POST["link"];
        $name=$_POST["name"];
        $picture=$_POST["picture"];
        query($db,"INSERT INTO `product`(`name`,`button`,`date`,`link`,`description`,`picture`)VALUES('$name','$button','$date','$link','$description','$picture')");
        $row=query($db,"SELECT*FROM `product`");
        echo(json_encode([
            "success"=>true,
            "data"=>$row[count($row)-1][0]
        ]));
    }
?>