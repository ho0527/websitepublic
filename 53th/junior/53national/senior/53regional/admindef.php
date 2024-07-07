<?php
    function up($row,$comp){
        usort($row,function($a,$b)use($comp){ return $a[$comp]>$b[$comp]||$a[$comp]==$b[$comp]&&$a[0]>$b[0]; });
        for($i=0;$i<count($row);$i++){
            if($row[$i][1]=="0000"){
                ?>
                <tr>
                    <td class="atd">
                        <?= $row[$i][1] ?>
                        <input type="button" class="but" onclick="location.href='edit.php?edit=<?= $row[$i][1] ?>'" value="修改" disabled>
                        <input type="button" class="but" onclick="location.href='edit.php?del=<?= $row[$i][1] ?>'" value="刪除" disabled>
                    </td>
                    <td class="atd"><?= $row[$i][2] ?></td>
                    <td class="atd"><?= $row[$i][3] ?></td>
                    <td class="atd"><?= $row[$i][4] ?></td>
                    <td class="atd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="atd">
                        <?= $row[$i][1] ?>
                        <input type="button" class="but" onclick="location.href='edit.php?edit=<?= $row[$i][1] ?>'" value="修改">
                        <input type="button" class="but" onclick="location.href='edit.php?del=<?= $row[$i][1] ?>'" value="刪除">
                    </td>
                    <td class="atd"><?= $row[$i][2] ?></td>
                    <td class="atd"><?= $row[$i][3] ?></td>
                    <td class="atd"><?= $row[$i][4] ?></td>
                    <td class="atd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }
        }
    }

    function down($row,$comp){
        usort($row,function($a,$b)use($comp){ return $a[$comp]<$b[$comp]||$a[$comp]==$b[$comp]&&$a[0]>$b[0]; });
        for($i=0;$i<count($row);$i++){
            if($row[$i][1]=="0000"){
                ?>
                <tr>
                    <td class="atd">
                        <?= $row[$i][1] ?>
                        <input type="button" class="but" onclick="location.href='edit.php?edit=<?= $row[$i][1] ?>'" value="修改" disabled>
                        <input type="button" class="but" onclick="location.href='edit.php?del=<?= $row[$i][1] ?>'" value="刪除" disabled>
                    </td>
                    <td class="atd"><?= $row[$i][2] ?></td>
                    <td class="atd"><?= $row[$i][3] ?></td>
                    <td class="atd"><?= $row[$i][4] ?></td>
                    <td class="atd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="atd">
                        <?= $row[$i][1] ?>
                        <input type="button" class="but" onclick="location.href='edit.php?edit=<?= $row[$i][1] ?>'" value="修改">
                        <input type="button" class="but" onclick="location.href='edit.php?del=<?= $row[$i][1] ?>'" value="刪除">
                    </td>
                    <td class="atd"><?= $row[$i][2] ?></td>
                    <td class="atd"><?= $row[$i][3] ?></td>
                    <td class="atd"><?= $row[$i][4] ?></td>
                    <td class="atd"><?= $row[$i][5] ?></td>
                </tr>
                <?php
            }
        }
    }

    function updown($row){
        if(@$_GET["nb"]=="降冪"){
            down($row,1);
            ?><script>document.getElementById("nb").value="升冪"</script><?php
        }elseif(@$_GET["un"]=="降冪"){
            down($row,2);
            ?><script>document.getElementById("un").value="升冪"</script><?php
        }elseif(@$_GET["n"]=="降冪"){
            down($row,4);
            ?><script>document.getElementById("n").value="升冪"</script><?php
        }elseif(@$_GET["un"]=="升冪"){
            up($row,2);
        }elseif(@$_GET["n"]=="升冪"){
            up($row,4);
        }else{
            up($row,1);
        }
    }
?>