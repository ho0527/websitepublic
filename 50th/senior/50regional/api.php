<?php
    include("link.php");
    $data=$_SESSION["data"];

    if(isset($_GET["logout"])){
        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','登出系統','$time','')");
        session_unset();
        ?><script>alert("登出成功");location.href="index.php"</script><?php
    }

    if(isset($_GET["projectdel"])){
        $id=$_SESSION["id"];
        $data=$_SESSION["data"];
        query($db,"DELETE FROM `project` WHERE `id`='$id'");
        query($db,"DELETE FROM `facing` WHERE `projectid`='$id'");
        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"刪除面向",$time,""]);
        ?><script>alert("刪除成功!");location.href="project.php"</script><?php
    }

    if(isset($_GET["key"])){
        if($_GET["key"]=="canpostopinion"){
            if($_GET["value"]=="true"||$_GET["value"]=="false"){
                $id=$_GET["id"];
                $value=$_GET["value"];
                query($db,"UPDATE `project` SET `canpostopinion`='$value' WHERE `id`='$id'");
                query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"停止或開啟發表意見功能",$time,"id=".$id]);
                ?><script>location.href="teamleader.php"</script><?php
            }
        }

        if($_GET["key"]=="planchange"){
            $value=$_GET["value"];
            $id=$_GET["id"];
            query($db,"UPDATE `project` SET `canplanscore`='$value' WHERE `id`='$id'");
            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"停止或開啟執行方案評分功能",$time,"id=".$id]);
            ?><script>location.href="teamleader.php"</script><?php
        }
    }

    if(isset($_GET["projectdata"])){
        $id=$_SESSION["data"];
        $projectdata=[];

        $rowproject=query($db,"SELECT*FROM `project`");
        for($i=0;$i<count($rowproject);$i=$i+1){
            $projectid=$rowproject[$i][0];
            $leader=$rowproject[$i][3];
            $member=explode("|&|",$rowproject[$i][4]);
            $status="";

            if($id==$leader||$id=="1"){
                $status="leader";
            }elseif(in_array($id,$member)){
                $status="member";
            }else{
                $status="false";
            }

            if($status!="false"){
                if($rowproject[$i][8]=="false"){
                    $projectdata[$i]=["projectid"=>$projectid,"status"=>$status,"data"=>"this project is not open"];
                }elseif($rowproject[$i][8]=="true"){
                    $projectdata[$i]=["projectid"=>$projectid,"status"=>$status];
                    $rowplan=query($db,"SELECT*FROM `plan` WHERE `projectid`='$projectid'");
                    if(count($rowplan)>0){
                        if($status=="leader"){
                            for($j=0;$j<count($rowplan);$j=$j+1){
                                $planid=$rowplan[$j][0];
                                $rowplanscore=query($db,"SELECT*FROM `planscore` WHERE `planid`='$planid'");
                                $nonescorelist=$member;
                                for($k=0;$k<count($rowplanscore);$k=$k+1){
                                    $planscoreid=$rowplanscore[$k][0];
                                    $userid=$rowplanscore[$k][1];
                                    $nonescorelist=array_diff($nonescorelist,[$userid]);
                                }
                                $nonescorelist=array_values($nonescorelist);
                                if(count($nonescorelist)==0){
                                    $projectdata[$i]["data"][]=["planid"=>$planid,"message"=>"true"];
                                }else{
                                    $projectdata[$i]["data"][]=["planid"=>$planid,"message"=>"false","userid"=>$nonescorelist];
                                }
                            }
                        }else{
                            for($j=0;$j<count($rowplan);$j=$j+1){
                                $planid=$rowplan[$j][0];
                                $rowplanscore=query($db,"SELECT*FROM `planscore` WHERE `planid`='$planid'");
                                $check=false;
                                $nonescorelist=$member;
                                for($k=0;$k<count($rowplanscore);$k=$k+1){
                                    $planscoreid=$rowplanscore[$k][0];
                                    $userid=$rowplanscore[$k][1];
                                    if($userid==$id){
                                        $check=true;
                                    }
                                }

                                if($check){
                                    $projectdata[$i]["data"][]=["planid"=>$planid,"message"=>"true"];
                                }else{
                                    $projectdata[$i]["data"][]=["planid"=>$planid,"message"=>"false"];
                                }
                            }
                        }
                    }else{
                        $projectdata[$i]=["projectid"=>$projectid,"status"=>$status,"data"=>"no plan in this project"];
                    }
                }elseif($rowproject[$i][8]=="check"){
                    $projectdata[$i]=["projectid"=>$projectid,"status"=>$status,"data"=>"this project is end score"];
                }else{
                    $projectdata[$i]=["projectid"=>$projectid,"status"=>$status,"data"=>"this project is end"];
                }
            }else{
                $projectdata[$i]=["projectid"=>$projectid,"status"=>$status,"data"=>"user not in this project"];
            }
        }

        echo(json_encode($projectdata));
    }

    if(isset($_GET["statistic"])){
        if($_GET["statistic"]=="useropinion"){
            $data=[];
            $rowopinion=query($db,"SELECT*FROM `opinion`");
            for($i=0;$i<count($rowopinion);$i=$i+1){
                $userid=$rowopinion[$i][1];
                $check=false;
                for($j=0;$j<count($data);$j=$j+1){
                    if($data[$j][0]==$userid){
                        $data[$j][1]=$data[$j][1]+1;
                        $check=true;
                        break;
                    }
                }
                if(!$check){
                    $data[]=[$userid,1];
                }
            }

            usort($data,function($a,$b){
                if($a[1]<$b[1]){
                    return 1;
                }else{
                    return 0;
                }
            });

            array_slice($data,0,3);

            for($i=0;$i<count($data);$i=$i+1){
                $userid=$data[$i][0];
                $rowscore=query($db,"SELECT*FROM `score` WHERE `userid`='$userid'");
                $score=0;
                $scoredata=["1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0];
                if(count($rowscore)>0){
                    for($j=0;$j<count($rowscore);$j=$j+1){
                        $scoredata[$rowscore[$j][3]]=$scoredata[$rowscore[$j][3]]+1;
                    }

                    $data[$i][]=$scoredata;
                }else{
                    $data[$i][]="無資料";
                }
            }
            echo(json_encode($data));
        }elseif($_GET["statistic"]=="projectopinion"){
            $data=[];
            $rowproject=query($db,"SELECT*FROM `project`");
            for($i=0;$i<count($rowproject);$i=$i+1){
                $projectid=$rowproject[$i][0];
                $count=0;
                $rowopinion=query($db,"SELECT*FROM `opinion`");
                for($j=0;$j<count($rowopinion);$j=$j+1){
                    if(explode("_",$rowopinion[$j][2])[0]==$projectid){
                        $count=$count+1;
                    }
                }
                $data[]=[$projectid,$count];
            }

            usort($data,function($a,$b){
                if($a[1]<$b[1]){
                    return 1;
                }else{
                    return 0;
                }
            });
            echo(json_encode($data));
        }elseif($_GET["statistic"]=="projectfacing"){
            $data=[];
            $rowproject=query($db,"SELECT*FROM `project`");
            for($i=0;$i<count($rowproject);$i=$i+1){
                $projectid=$rowproject[$i][0];
                $facingdata=[];
                $count=0;
                $rowfacing=query($db,"SELECT*FROM `facing` WHERE `projectid`='$projectid'");
                for($j=0;$j<count($rowfacing);$j=$j+1){
                    $facingdata[]=[$rowfacing[$j][0],0];
                }
                $rowopinion=query($db,"SELECT*FROM `opinion`");
                for($j=0;$j<count($rowopinion);$j=$j+1){
                    if(explode("_",$rowopinion[$j][2])[0]==$projectid){
                        $count=$count+1;
                    }
                    for($k=0;$k<count($facingdata);$k=$k+1){
                        if(explode("_",$rowopinion[$j][2])[1]==$facingdata[$k][0]){
                            $facingdata[$k][1]=$facingdata[$k][1]+1;
                        }
                    }
                }
                $data[]=[$projectid,$count,$facingdata];
            }

            usort($data,function($a,$b){
                if($a[1]<$b[1]){
                    return 1;
                }else{
                    return 0;
                }
            });
            echo(json_encode($data));
        }
    }
?>