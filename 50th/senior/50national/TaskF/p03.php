<?php
    $memoryBefore=memory_get_usage();
    echo("p03\n");

    $data=[];
    // 讀取迷宮資料
    for($i=0;$i<8;$i=$i+1){
        $data[]=explode(" ",trim(fgets(STDIN)));
        for($j=0;$j<8;$j=$j+1){
            $data[$i][$j]=(int)$data[$i][$j];
        }
    }

    // 定義八個移動方向的位移
    $dx=[-1,-1,0,1,1,1,0,-1];
    $dy=[0,1,1,1,0,-1,-1,-1];


    $visited = [];
    $path = "(0,0)\n";
    $stack = [[0, 0]];
    $foundExit = false;

    // 走迷宮
    for ($i = 0; $i < 8; $i++) {
        $visited[$i] = [];
        for ($j = 0; $j < 8; $j++) {
            $visited[$i][$j] = false;
        }
    }

    // 走迷宮
    for ($i = 0; $i < 8; $i++) {
        $visited[$i] = [];
        for ($j = 0; $j < 8; $j++) {
            $visited[$i][$j] = false;
        }
    }

    // 走迷宮
    while (!empty($stack)) {
        $curr=array_pop($stack);
        $x=$curr[0];
        $y=$curr[1];
        $visited[$x][$y]=true;

        // 檢查是否到達出口
        if($x==7&&$y==7){
            $check=true;
            break;
        }

        // 嘗試八個移動方向
        for($i=0;$i<8;$i=$i+1){
            $nx=$x+$dx[$i];
            $ny=$y+$dy[$i];

            // 檢查下一步是否在迷宮範圍內且可通行且未被走過
            if($nx>=0&&$nx<8&&$ny>=0&&$ny<8&&$data[$nx][$ny]==0&&!$visited[$nx][$ny]){
                $stack[]=[$nx,$ny];
                $path .= "($nx,$ny)\n";
            }
        }
    }

    // 輸出結果
    if($check){
        echo($path.PHP_EOL);
    }else{
        echo("[WARNING]No path found to the exit".PHP_EOL);
    }

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>