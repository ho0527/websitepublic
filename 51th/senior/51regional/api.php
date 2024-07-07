<?php
    include("link.php");
    if(isset($_SESSION["data"])){
        $data=$_SESSION["data"];
    }

    if(isset($_GET["logout"])){
        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','登出系統','$time','')");
        session_unset();
        ?><script>alert("登出成功");location.href="index.php"</script><?php
    }

    if(isset($_GET["formdel"])){
        $id=$_GET["id"];
        query($db,"UPDATE `question` SET `ps`='del' WHERE `id`='$id'");
        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','刪除問卷','$time','qid=$id')");
        ?><script>location.href="admin.php"</script><?php
    }

    if(isset($_GET["cancel"])){
        unset($_SESSION["id"]);
        unset($_SESSION["count"]);
        ?><script>location.href="admin.php"</script><?php
    }

    if(isset($_GET["getresponselist"])){
        $row=query($db,"SELECT*FROM `response` WHERE `questionid`=?",[$_SESSION["id"]]);
        echo(json_encode([
            "success"=>true,
            "data"=>$row
        ]));
    }

    if(isset($_GET["delresponse"])){
        $row=query($db,"SELECT*FROM `response` WHERE `id`=?",[$_GET["id"]]);
        if($row){
            query($db,"DELETE FROM `response` WHERE `id`=?",[$_GET["id"]]);
            $questionrow=query($db,"SELECT*FROM `question` WHERE `id`=?",[$row[0][2]]);
            query($db,"UPDATE `question` SET `responcount`=? WHERE `id`=?",[(int)$questionrow[0][4]-1,$row[0][2]]);
            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','刪除回應','$time','qid=$id')");
            ?><script>alert("刪除成功");location.href="responselist.html"</script><?php
        }else{
            ?><script>alert("查無回應");location.href="responselist.html"</script><?php
        }
    }

    if(isset($_GET["getquestion"])){
        $maxlen=(int)$_GET["maxlen"];
        $page=(int)$_GET["page"];
        $data=[];
        $dataoffset=[];

        $row=query($db,"SELECT*FROM `question` WHERE `id`=?",[$_GET["id"]]);
        $question=json_decode($row[0][7]);
        for($i=0;$i<count($question);$i=$i+1){
            if($question[$i][3]&&$question[$i][3]!="none"){
                $data[]=$question[$i];
            }
        }

        if($maxlen==-1){
            $dataoffset=$data;
            $maxpage=1;
        }else{
            $offset=(int)$page*(int)$maxlen;
            for($i=$offset;$i<min(count($data),$maxlen+$offset);$i=$i+1){
                $dataoffset[]=$data[$i];
            }
            $maxpage=ceil(count($data)/$maxlen);
        }

        if($row){
            echo(json_encode([
                "success"=>true,
                "data"=>$dataoffset,
                "maxpage"=>$maxpage
            ]));
        }else{
            echo(json_encode([
                "success"=>false,
                "data"=>"查無資料"
            ]));
        }
    }

    if(isset($_GET["getresponse"])){
        $row=query($db,"SELECT*FROM `response` WHERE `id`=?",[$_GET["id"]]);

        if($row){
            echo(json_encode([
                "success"=>true,
                "data"=>$row[0]
            ]));
        }else{
            echo(json_encode([
                "success"=>false,
                "data"=>"查無資料"
            ]));
        }
    }
?>