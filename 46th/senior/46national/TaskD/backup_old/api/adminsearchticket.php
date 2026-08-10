<?php
    include("../link.php");
    if(isset($_GET["get"])){
        // 拿值
        $page=$_GET["page"];
        $offset=(int)($page-1)*5;
        $row=query($db,"SELECT*FROM `ticket` LIMIT 5 OFFSET $offset");
        if(isset($_GET["phone"])){
            $row=query($db,"SELECT*FROM `ticket` WHERE `phone`=? LIMIT 5 OFFSET $offset",[$_GET["phone"]]);
        }elseif(isset($_GET["code"])){
            $row=query($db,"SELECT*FROM `ticket` WHERE `code`=? LIMIT 5 OFFSET $offset",[$_GET["code"]]);
        }

        // 主程式
        $maindata=[];
        $maxtotal=count(query($db,"SELECT*FROM `ticket`"));
        for($i=0;$i<count($row);$i=$i+1){
            $trainname=query($db,"SELECT*FROM `train` WHERE `id`=?",[$row[$i][1]])[0][2];
            $arrivetime=query($db,"SELECT*FROM `stop` WHERE `trainid`=?AND`stationid`=?",[$row[$i][1],$row[$i][3]])[0][4];
            $startstation=query($db,"SELECT*FROM `station` WHERE `id`=?",[$row[$i][3]])[0][2];
            $endstation=query($db,"SELECT*FROM `station` WHERE `id`=?",[$row[$i][4]])[0][2];
            $delinnerhtml="<input type='button' class='bluebutton cancel' id='".$row[$i][0]."' value='取消'>";
            if($row[$i]["statu"]==0){
                $delinnerhtml="該列車已完成發車";
            }else if($row[$i]["statu"]==-1){
                $delinnerhtml="<div class='cancel'>已取消 取消時間:<br>".$row[$i]["deletetime"]."</div>";
            }
            $maindata[]=[
                "code"=>$row[$i][5],
                "createdate"=>$row[$i][9],
                "arrivetime"=>$arrivetime,
                "code"=>$trainname,
                "startstation"=>$startstation."站",
                "endstation"=>$endstation."站",
                "count"=>$row[$i][7],
                "delinnerhtml"=>$delinnerhtml
            ];
        }
        echo(json_encode([
            "success"=>true,
            "maxtotal"=>$maxtotal,
            "data"=>$maindata
        ]));
    }
?>