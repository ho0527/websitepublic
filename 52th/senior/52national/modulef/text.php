<?php
$num_a = readline();
$data_a = [];
$ans = [];
$i = 0;
while($i<$num_a){
    $value = explode(" ", readline());
    $key = $value[0];
    array_shift($value);
    $data_a[$key] = $value;
    $i++;
}
$num_b = readline();
$data_b = [];
$i = 0;
while($i<$num_b){
    $data_b[] = explode(" ",readline());
    $i++;
}
foreach ($data_b as $bla){
    switch ($bla[0]){
        case "A":
            if ($bla[1] == "TWD") {
                $ans[] = numFormat(($bla[3] / $data_a[$bla[2]][3]));
            }else{
                $ans[] = numFormat(($bla[3] * $data_a[$bla[1]][2]));
            }
            break;
        case "C":
            if ($bla[1] == "TWD") {
                $ans[] = numFormat(($bla[3] / $data_a[$bla[2]][1]));
            }else{
                $ans[] = numFormat(($bla[3] * $data_a[$bla[1]][0]));
            }
            break;
    }
}

foreach ($ans as $bla){
    print ($bla."\n");
}

function numFormat($num){
    return number_format(floor(($num) * 100000) / 100000,5,'.','');
}
?>