<?php
    function up($row,$com){
        usort($row,function($a,$b)use($com){
            if($a[$com]>$b[$com]||$a[$com]==$b[$com]&&$a[0]>$b[0]){
                return 1;
            }
        });
        for($i=0;$i<count($row);$i++){
            if($row[$i][1]=="a0000"){
                ?>
                <tr>
                    <td class="admintd">
                        <?= $row[$i][1] ?>
                        <input type="button" onclick="location.href='signupedit.php?edit=<?= $row[$i][1] ?>'" value="修改" disabled>
                        <input type="button" onclick="location.href='signupedit.php?del=<?= $row[$i][1] ?>'" value="刪除" disabled>
                    </td>
                    <td class="admintd"><?= $row[$i][2] ?></td>
                    <td class="admintd"><?= $row[$i][3] ?></td>
                    <td class="admintd"><?= $row[$i][4] ?></td>
                    <td class="admintd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="admintd">
                        <?= $row[$i][1] ?>
                        <input type="button" onclick="location.href='signupedit.php?edit=<?= $row[$i][1] ?>'" value="修改">
                        <input type="button" onclick="location.href='signupedit.php?del=<?= $row[$i][1] ?>'" value="刪除">
                    </td>
                    <td class="admintd"><?= $row[$i][2] ?></td>
                    <td class="admintd"><?= $row[$i][3] ?></td>
                    <td class="admintd"><?= $row[$i][4] ?></td>
                    <td class="admintd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }
        }
    }

    function down($row,$com){
        usort($row,function($a,$b)use($com){
            if($a[$com]<$b[$com]||$a[$com]==$b[$com]&&$a[0]>$b[0]){
                return 1;
            }
        });
        for($i=0;$i<count($row);$i++){
            if($row[$i][1]=="a0000"){
                ?>
                <tr>
                    <td class="admintd">
                        <?= $row[$i][1] ?>
                        <input type="button" onclick="location.href='signupedit.php?edit=<?= $row[$i][1] ?>'" value="修改" disabled>
                        <input type="button" onclick="location.href='signupedit.php?del=<?= $row[$i][1] ?>'" value="刪除" disabled>
                    </td>
                    <td class="admintd"><?= $row[$i][2] ?></td>
                    <td class="admintd"><?= $row[$i][3] ?></td>
                    <td class="admintd"><?= $row[$i][4] ?></td>
                    <td class="admintd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="admintd">
                        <?= $row[$i][1] ?>
                        <input type="button" onclick="location.href='signupedit.php?edit=<?= $row[$i][1] ?>'" value="修改">
                        <input type="button" onclick="location.href='signupedit.php?del=<?= $row[$i][1] ?>'" value="刪除">
                    </td>
                    <td class="admintd"><?= $row[$i][2] ?></td>
                    <td class="admintd"><?= $row[$i][3] ?></td>
                    <td class="admintd"><?= $row[$i][4] ?></td>
                    <td class="admintd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }
        }
    }

    function updown($row){
        if(@$_GET["udnb"]=="升冪"){
            down($row,1);
            ?><script>document.getElementById("udnb").value="降冪"</script><?php
        }elseif(@$_GET["udun"]=="升冪"){
            down($row,2);
            ?><script>document.getElementById("udun").value="降冪"</script><?php
        }elseif(@$_GET["udn"]=="升冪"){
            down($row,4);
            ?><script>document.getElementById("udn").value="降冪"</script><?php
        }elseif(@$_GET["udun"]=="降冪"){
            up($row,2);
        }elseif(@$_GET["udn"]=="降冪"){
            up($row,4);
        }else{
            up($row,1);
        }
    }

    function data($row,$i,$p){
        if($p=="name"){
            ?>商品名稱: <?= $row[$i][2] ?><?php
        }elseif($p=="cost"){
            ?>費用: <?= $row[$i][3] ?><?php
        }elseif($p=="link"){
            ?>相關連結: <?= $row[$i][4] ?><?php
        }elseif($p=="date"){
            ?>發佈日期: <?= $row[$i][5] ?><?php
        }else{
            ?>商品簡介: <?= $row[$i][6] ?><?php
        }
    }
?>