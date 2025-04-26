<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>資料視覺化</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="grid">
            <div class="showtop">
                <table class="table" id="table">
                    <tr>
                        <th class="td">question</th>
                        <th class="td">actual answer</th>
                        <th class="td">submit answer</th>
                    </tr>
                    <?php
                        $ans=fopen("actualAnswers.csv","r");
                        $output=fopen("submittedAnswers.csv","r");
                        $i=0;
                        $count=0;

                        while(($dataans=fgetcsv($ans))&&($dataoutput=fgetcsv($output))){
                            ?>
                            <tr>
                                <td class="td"><?= $i+1; ?></td>
                                <td class="td"><?= $dataans[0]; ?></td>
                                <td class="td"><?= $dataoutput[0]; ?></td>
                            </tr>
                            <?php

                            if($dataans[0]==$dataoutput[0]){
                                $count=$count+1;
                            }

                            $i=$i+1;
                        }

                        fclose($ans);
                        fclose($output);
                    ?>
                </table>
            </div>
            <div class="showbottom" id="score">Score: <?= $count; ?>/<?= $i; ?></div>
        </div>
    </body>
</html>