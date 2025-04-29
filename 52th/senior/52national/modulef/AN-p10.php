<?php
    $memoryBefore=memory_get_usage();
    echo("p10-an\n");
    // 讀取的m和n的值
    $m=explode(" ",trim(fgets(STDIN)));
    // 讀取第二行的股票價格
    $prices=explode(" ",trim(fgets(STDIN)));
    // 定義初始最大利益為0
    $max=0;
    // 進行每一次交易
    for($i=0;$i<$m[1];$i=$i+1){
        // 從當前位置遍歷到最後一天
        for($j=$i+1;$j<$m[1];$j=$j+1){
            // 如果當前交易次數已經等於m，則不再進行交易
            if($j-$i<=$m[0]){
                // 計算利益
                $profit=$prices[$j]-$prices[$i];
                // 更新最大利益
                $max=max($max,$profit);
            }
        }
    }
    // 輸出最大利益
    echo($max.PHP_EOL);
    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>