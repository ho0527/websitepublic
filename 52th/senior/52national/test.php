<?php
$n = 0;
fscanf(STDIN, "%d\n", $n);

if (1 <= $n && $n <= (2 ** 31 - 1)) {
    $ans = [];
    $max = 0;

    for ($i = 0; $i < $n; $i++) {
        fscanf(STDIN, "%d\n", $num);

        if (isset($ans[$num])) {
            $ans[$num]++;
        } else {
            $ans[$num] = 1;
        }

        if ($ans[$num] > $max) {
            $max = $ans[$num];
        }
    }

    if ($max == 1) {
        echo "-1" . PHP_EOL;
    } else {
        ksort($ans);

        foreach ($ans as $key => $value) {
            if ($value == $max) {
                echo $key . PHP_EOL;
            }
        }
    }
} else {
    echo "輸入未符合要求";
}
?>