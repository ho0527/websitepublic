<?php
    $memorybefore=memory_get_usage();

    echo("p01\n");
    // 讀取輸入
    $n=(int)trim(fgets(STDIN));
    $data=[];
    for($i=0;$i<$n;$i=$i+1){
        $data[]=str_split(trim(fgets(STDIN)));
    }

    // 判斷輸贏
    $winner="?";
    $wincheck=0;

    // 檢查橫排
    for($i=0;$i<$n;$i=$i+1){
        $player=$data[$i][0];
        $win=true;
        for($j=1;$j<$n;$j=$j+1){
            if($data[$i][$j]!=$player){
                $win=false;
                break;
            }
        }
        if($win&&$player!="-"){
            $winner=$player;
            $wincheck=$wincheck+1;
        }
    }

    // 檢查直排
    for($i=0;$i<$n;$i=$i+1){
        $player=$data[0][$i];
        $win=true;
        for($j=1;$j<$n;$j=$j+1){
            if($data[$j][$i]!=$player){
                $win=false;
                break;
            }
        }
        if($win&&$player!="-"){
            $winner=$player;
            $wincheck=$wincheck+1;
        }
    }

    // 檢查對角線
    $player=$data[0][0];
    $win=true;
    for($i=0;$i<$n;$i=$i+1){
        if($player!=$data[$i][$i]){
            $win=false;
        }
    }

    if($win&&$player!="-"){
        $winner=$player;
        $wincheck=$wincheck+1;
    }

    $player=$data[0][$n-1];
    $win=true;
    for($i=0;$i<$n;$i=$i+1){
        if($player!=$data[$i][$n-1-$i]){
            $win=false;
        }
    }

    if($win&&$player!="-"){
        $winner=$player;
        $wincheck=$wincheck+1;
    }

    if($wincheck>1){
        $winner="?";
    }

    // 輸出結果
    echo($winner.PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>