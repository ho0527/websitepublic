<?php
    $memorybefore=memory_get_usage();

    echo("p01\n");

    // 讀取輸入的五子棋盤面
    $data=[];
    $ans="?";
    $anscount=0;

    for($i=0;$i<15;$i=$i+1){
        $data[]=trim(fgets(STDIN));
    }

    // 檢查水平方向是否有五子連線
    for($i=0;$i<15;$i=$i+1){
        if(strpos($data[$i],"BBBBB")!=false){
            $ans="B";
            $anscount=$anscount+1;
        }
        if(strpos($data[$i],"WWWWW")!=false){
            $ans="W";
            $anscount=$anscount+1;
        }
    }

    // 檢查垂直方向是否有五子連線
    for($i=0;$i<15;$i=$i+1){
        $linedata="";
        for($j=0;$j<15;$j=$j+1){
            $linedata=$linedata.$data[$j][$i];
        }
        if(strpos($linedata,"BBBBB")!=false){
            $ans="B";
            $anscount=$anscount+1;
        }
        if(strpos($linedata,"WWWWW")!=false){
            $ans="W";
            $anscount=$anscount+1;
        }
    }

    // 檢查主對角線方向是否有五子連線
    for($i=0;$i<11;$i=$i+1){
        for($j=0;$j<11;$j=$j+1){
            $linedata="";
            for($k=0;$k<5;$k=$k+1){
                $linedata=$linedata.$data[$i+$k][$j+$k];
            }
            if($linedata=="BBBBB"){
                $ans="B";
                $anscount=$anscount+1;
            }
            if($linedata=="WWWWW"){
                $ans="W";
                $anscount=$anscount+1;
            }
        }
    }

    // 檢查副對角線方向是否有五子連線
    for($i=0;$i<11;$i=$i+1){
        for($j=4;$j<15;$j=$j+1){
            $linedata="";
            for($k=0;$k<5;$k=$k+1){
                $linedata=$linedata.$data[$i+$k][$j-$k];
            }
            if($linedata=="BBBBB"){
                $ans="B";
                $anscount=$anscount+1;
            }
            if($linedata=="WWWWW"){
                $ans="W";
                $anscount=$anscount+1;
            }
        }
    }

    if($anscount==1){
        echo($ans.PHP_EOL);
    }else{
        echo("?".PHP_EOL);
    }

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>