<?php
    include("link.php");
    if(isset($_GET["logout"])){
        if(isset($_SESSION["data"])){
            ?><script>alert("登出成功!");location.href="index.html"</script><?php
            session_unset();
        }else{
            ?><script>alert("請先登入!");location.href="index.html"</script><?php
        }
    }

    if(isset($_GET["traintypelist"])){
        $row=query($db,"SELECT*FROM `type`");
        echo(json_encode($row));
    }

    if(isset($_GET["traincodelist"])){
        $row=query($db,"SELECT*FROM `train` WHERE `ps`!='delete'");
        $data=[];
        for($i=0;$i<count($row);$i=$i+1){
            $data[]=$row[$i][2];
        }
        echo(json_encode($data));
    }

    if(isset($_GET["stationlist"])){
        $row=query($db,"SELECT*FROM `station`");
        echo(json_encode($row));
    }

    if(isset($_GET["stationlistid1"])){
        $row=query($db,"SELECT*FROM `station`");
        $data=[];
        for($i=0;$i<count($row);$i=$i+1){
            $data[$row[$i][0]]=[
                "englishname"=>$row[$i][1],
                "name"=>$row[$i][2]
            ];
        }
        echo(json_encode($data));
    }

    if(isset($_GET["trainlist"])){
        $row=query($db,"SELECT*FROM `train` WHERE `ps`!='delete'");
        $stoprow=query($db,"SELECT*FROM `stop`");
        $stationrow=query($db,"SELECT*FROM `station`");
        echo(json_encode([$row,$stoprow,$stationrow]));
    }

    if(isset($_GET["seatlist"])){
        $row=query($db,"SELECT*FROM `ticket`");
        $data=[];
        for($i=0;$i<count($row);$i=$i+1){
            $data[]="";
        }
        echo(json_encode($data));
    }

    if(isset($_GET["key"])){
        /*
        statu:
        ok(1): 已訂票
        end(0): 已結束
        delete(-1): 被刪除
        */
        if($_GET["key"]=="deltrain"){
            $id=$_GET["id"];
            if(query($db,"SELECT*FROM `ticket` WHERE `trainid`=?AND`statu`='1'",[$id])){
                ?><script>if(confirm("列車有被訂票是否繼續刪除?")){ location.href="api.php?deltrain=&id=<?php echo($id) ?>" }else{ location.href="admintrain.html" }</script><?php
            }else{
                ?><script>location.href="api.php?deltrain=&id=<?php echo($id) ?>"</script><?php
            }
        }
    }

    if(isset($_GET["deltrain"])){
        $id=$_GET["id"];
        $row=query($db,"SELECT*FROM `ticket` WHERE `trainid`=?AND`statu`='1'",[$id]);
        for($i=0;$i<count($row);$i=$i+1){
            $startstation=query($db,"SELECT*FROM `station` WHERE `id`=?",[$row[$i][3]])[0][2];
            $endstation=query($db,"SELECT*FROM `station` WHERE `id`=?",[$row[$i][4]])[0][2];
            $traincode=query($db,"SELECT*FROM `train` WHERE `id`='$id'")[0][2];

            // 簡訊部分
            $file=fopen("SMS/".$row[$i][6].".txt","a");
            fwrite($file,"========================================\n您所訂的列車已經取消發車。訂票編號: ".$row[$i][5]."，".$row[$i][10]."\n".$startstation."站 到 ".$endstation."站 ".$traincode."車次，請改搭其他列車\n");
            fclose($file);

            query($db,"UPDATE `ticket` SET `statu`='-1',`deletetime`='$time' WHERE `id`=?",[$row[$i][0]]);
        }
        query($db,"UPDATE `train` SET `ps`='delete' WHERE `id`=?",[$id]);
        query($db,"UPDATE `stop` SET `ps`='delete' WHERE `trainid`=?",[$id]);
        ?><script>alert("刪除成功!");location.href="admintrain.html"</script><?php
    }

    if(isset($_GET["ticket"])){
        $field="all";

        if(isset($_GET["phone"])){
            $field="phone";
            $data=$_GET["phone"];
        }elseif(isset($_GET["code"])){
            $field="code";
            $data=$_GET["code"];
        }

        if($field=="all"){
            $row=query($db,"SELECT*FROM `ticket`");
        }else{
            $row=query($db,"SELECT*FROM `ticket` WHERE `$field`=?",[$data]);
        }

        echo(json_encode([
            "success"=>true,
            "data"=>$row
        ]));
    }

    if(isset($_GET["cancelticket"])){
        $id=$_GET["id"];
        $row=query($db,"UPDATE `ticket` SET `statu`='-1',`deletetime`=? WHERE `id`='$id'",[$time]);
        ?><script>alert("取消成功");location.href="search.html"</script><?php
    }

    if(isset($_GET["admincancelticket"])){
        $id=$_GET["id"];
        $row=query($db,"SELECT*FROM `ticket` WHERE `id`='$id'")[0];
        $deltime=$time;

        $startstation=query($db,"SELECT*FROM `station` WHERE `id`=?",[$row[3]])[0][2];
        $endstation=query($db,"SELECT*FROM `station` WHERE `id`=?",[$row[4]])[0][2];
        $traincode=query($db,"SELECT*FROM `train` WHERE `id`='$id'")[0][2];

        // 簡訊部分
        $file=fopen("SMS/".$row[6].".txt","a");
        fwrite($file,"========================================\n您的訂票紀錄已被管理員取消。訂票編號: ".$row[5]."，".$row[10]."\n".$startstation."站 到 ".$endstation."站 ".$traincode."車次，取消時間: $deltime\n");
        fclose($file); 

        query($db,"UPDATE `ticket` SET `statu`='-1',`deletetime`='$deltime' WHERE `id`='$id'");
        ?><script>alert("取消成功");location.href="adminticket.html"</script><?php
    }
?>