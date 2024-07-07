<?php
    $memorybefore=memory_get_usage();

    echo("p07\n");

    // 讀取起始單字和終止單字
    $input=explode(" ",trim(fgets(STDIN)));
    $start=$input[0];
    $end=$input[1];

    // 讀取單字表數量和單字表
    $n=(int)trim(fgets(STDIN));
    $data=[];

    for($i=0;$i<$n;$i=$i+1){
        $data[]=trim(fgets(STDIN));
    }

    $ans=0; // 無法轉換到終止單字，返回 0

    // 將所有可能的單字組合成二維陣列
    $combination=[];
    $leanghdata=[];
    for($i=0;$i<count($data);$i=$i+1){
        $str=[$start];
        for($j=0;$j<strlen($data[$i]);$j=$j+1){
            if($start[$j]!=$data[$i][$j]){
                $str[]=substr_replace($start,$data[$i][$j],$j,1);
            }
        }
        $combination[]=$str;
    }

    // 比較所有單字鏈的長度，找出最短的
    for($i=0;$i<$combination;$i=$i+1){
        if($combination[$i][count($combination[$i])-1]==$end){
            $leanghdata[]=count($combination);
        }
    }

    sort($leanghdata);

    print_r($combinations);
    echo($leanghdata[0].PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter - $memorybefore;
    echo("memory used " . ($memorydifference / 1048576) . "MB");
?>
<?php
//     $memorybefore=memory_get_usage();

//     echo("p07\n");

//     // 讀取起始單字和終止單字
//     $word=explode(" ",trim(fgets(STDIN)));
//     $start=$word[0];
//     $end=$word[1];

//     // 讀取單字表數量和單字表
//     $n=(int)trim(fgets(STDIN));
//     $data=[];
//     $worddata=[];

//     for($i=0;$i<$n;$i=$i+1){
//         $data[]=trim(fgets(STDIN));
//     }

//     // $maindata=[];
//     $ans=0;// 無法轉換到終止單字，返回 0
//     // 將單字表轉換為集合，以便快速查找
//     $maindata=array_flip($data);

//     // 如果終止單字不在單字表中，無法轉換，返回 0
//     if(!isset($maindata[$end])){
//         $ans=0;
//     }else{
//         // 初始化佇列，起始單字和其轉換步數
//         $queue=[[$start,1]];

//         // 開始廣度優先搜索
//         while(!empty($queue)){
//             [$word,$steps]=array_shift($queue);

//             if($word==$end){
//                 $ans=$steps; // 找到終止單字，返回步數
//                 break;
//             }

//             // 對於當前單字的每個字母進行替換
//             for($i=0;$i<strlen($word);$i=$i+1){
//                 $oldword=$word[$i];
//                 // 替換為不同的字母
//                 for($j=0;$j<26;$j=$j+1){
//                     $newword=chr(ord("a")+$j);

//                     if($newword!=$oldword){
//                         // 替換字母
//                         $newWord=substr_replace($word,$newword,$i,1);

//                         // 如果新單字在單字表中，將其加入佇列並從單字表中移除
//                         if(isset($maindata[$newWord])){
//                             $queue[]=[$newWord,$steps+1];
//                             unset($maindata[$newWord]);
//                         }
//                     } // 跳過相同的字母
//                 }
//             }
//         }
//     }

//     echo($ans.PHP_EOL);

//     // $ans=0;
//     // // 建立單字圖
//     // for($i=0;$i<count($data);$i=$i+1){
//     //     for($j=0;$j<strlen($start);$j=$j+1){
//     //         $count=0;
//     //         for($k=0;$k<strlen($data[$i]);$k=$k+1){
//     //             if($start[$j]!=$data[$i][$k]){
//     //                 $count=$count+1;
//     //             }
//     //         }
//     //         if($count==1){ $maindata[]=$data[$i];}
//     //     }
//     // }

//     // 輸出結果

//     $memoryafter=memory_get_usage();
//     $memorydifference=$memoryafter-$memorybefore;
//     echo("memory used ".($memorydifference/1048576)."MB");
?>
