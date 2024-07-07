<?php
    include("../link.php");
    $userdata=$_SESSION["data"];
    $id=$_GET["id"];
    $data=[];
    $opinionrow=query($db,"SELECT*FROM `opinion` WHERE `id`='$id'")[0];
    $userrow=query($db,"SELECT*FROM `user` WHERE `id`=?",[$opinionrow[1]])[0];
    $scorerow=query($db,"SELECT*FROM `score` WHERE `opinionid`='$id'");
    $totalscore=0;
    $count=count($scorerow);
    $usercheck=true;
    for($j=0;$j<$count;$j=$j+1){
        $totalscore=$totalscore+$scorerow[$j][3];
        $scoreuserid=$scorerow[$j][1];
        $scoreuseerrow=query($db,"SELECT*FROM `user` WHERE `id`='$scoreuserid'")[0];
        if($scoreuseerrow[0]==$userdata){ $usercheck=false; }
    }
    if($count>0){ $averagescore=$totalscore/$count; }
    else{ $averagescore=0; }

    $extendidlist=explode("|&|",$opinionrow[3]);
    $extend=[];
    if($extendidlist[0]!=""){
        for($j=0;$j<count($extendidlist);$j=$j+1){
            $extendid=$extendidlist[$j];
            $extenopinionrow=query($db,"SELECT*FROM `opinion` WHERE `id`='$extendid'")[0];
            $extend[]="<a href='#opinion".$extendid."'>".$extenopinionrow[4]."</a>";
        }
        if(count($extend)>0){ $extend=implode(",",$extend); }
    }else{ $extend="無引用"; }

    $data=[$opinionrow,$extend,$userrow,[$averagescore,$count,$usercheck]];

    echo(json_encode($data));
?>