<?php
    function uper($todo){
        while($row=mysqli_fetch_row($todo)){
            $start_time=substr($row[3],0,2)*60+substr($row[3],3,5);
            $hr=substr($row[4],0,2)-substr($row[3],0,2);
            $min=substr($row[4],3,5)-substr($row[3],3,5);
            ?>
            <form>
                <div class="work-box" id="upbox<?= $row[0]; ?>" draggable="true" value="<?= $row[0]; ?>" style="height:<?= ($hr*30)+(($min/30)*15); ?>px;top:<?= 145+($start_time/2)-12; ?>px;left:180px;">
                    <button type="submit" id="button3" class="todobut" name="edit" value="<?= $row[0]; ?>">edit</button>
                    <button type="submit" id="button4" class="todobut" name="preview" value="<?= $row[0]; ?>">預覽</button>
                    標題: <?php echo($row[1]); ?>
                    <div id="upbox<?= $row[0] ?>starttime">開始時間: <?php echo($row[3]); ?></div>
                    <div id="upbox<?= $row[0] ?>endtime">結束時間: <?php echo($row[4]); ?></div>
                    處理狀態: <?php echo($row[5]); ?><br>
                    優先順序: <?php echo($row[6]); ?><br>
                    詳細內容: <?php echo($row[7]); ?><br>
                </div>
            </form>
            <?php
        }
    }

    function lower($todo){
        while($row=mysqli_fetch_row($todo)){
            $end_time=substr($row[3],0,2)*60+substr($row[3],3,5);
            $hr=substr($row[4],0,2)-substr($row[3],0,2);
            $min=substr($row[4],3,5)-substr($row[3],3,5);
            ?>
            <form>
                <div class="work-box" id="downbox<?= $row[0]; ?>" draggable="true" value="<?= $row[0]; ?>" style="height:<?= ($hr*30)+(($min/30)*15); ?>px;bottom:<?= (($end_time)/2)+85;//275 ?>px;left:180px;">
                    <button type="submit" id="button3" class="todobut" name="edit" value="<?= $row[0]; ?>">edit</button>
                    <button type="submit" id="button4" class="todobut" name="preview" value="<?= $row[0]; ?>">預覽</button>
                    標題: <?php echo($row[1]); ?>
                    <div id="downbox<?= $row[0] ?>starttime">開始時間: <?php echo($row[3]); ?></div>
                    <div id="downbox<?= $row[0] ?>endtime">結束時間: <?php echo($row[4]); ?></div>
                    處理狀態: <?php echo($row[5]); ?><br>
                    優先順序: <?php echo($row[6]); ?><br>
                    詳細內容: <?php echo($row[7]); ?><br>
                </div>
            </form>
            <?php
        }
    }

    function up($data,$comper){
        $a=[];
        while($row=mysqli_fetch_assoc($data)){
            array_push($a,$row);
        }
        for($i=0;$i<count($a)-1;$i=$i+1){
            for($j=0;$j<count($a)-$i-1;$j=$j+1){
                if($a[$j][$comper]>$a[$j+1][$comper]){
                    $tamp=$a[$j];
                    $a[$j]=$a[$j+1];
                    $a[$j+1]=$tamp;
                }
            }
        }
        for($row=0;$row<count($a);$row=$row+1){
            ?>
            <tr>
                <td class="usertable" id="<?= $a[$row]["id"]; ?>">
                    <?php print_r($a[$row]["id"]); ?>
                    <button type="submit" name="edit" value="<?= $a[$row]["id"]; ?>">edit</button>
                    <button type="submit" name="preview" value="<?= $a[$row]["id"]; ?>">預覽</button>
                </td>
                <td class="usertable"><?php print_r($a[$row]["title"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["date"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["start_time"]."~".$a[$row]["end_time"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["deal"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["priority"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["detail"]); ?></td>
            </tr>
            <?php
        }
    }

    function down($data,$comper){
        $a=[];
        while($row=mysqli_fetch_assoc($data)){
            array_push($a,$row);
        }
        for($i=0;$i<count($a)-1;$i=$i+1){
            for($j=0;$j<count($a)-$i-1;$j=$j+1){
                if($a[$j][$comper]<$a[$j+1][$comper]){
                    $tamp=$a[$j];
                    $a[$j]=$a[$j+1];
                    $a[$j+1]=$tamp;
                }
            }
        }
        for($row=0;$row<count($a);$row=$row+1){
            ?>
            <tr>
                <td class="usertable" id="<?= $a[$row]["id"]; ?>">
                    <?php print_r($a[$row]["id"]); ?>
                    <button type="submit" name="edit" value="<?= $a[$row]["id"]; ?>">edit</button>
                    <button type="submit" name="preview" value="<?= $a[$row]["id"]; ?>">預覽</button>
                </td>
                <td class="usertable"><?php print_r($a[$row]["title"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["date"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["start_time"]."~".$a[$row]["end_time"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["deal"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["priority"]); ?></td>
                <td class="usertable"><?php print_r($a[$row]["detail"]); ?></td>
            </tr>
            <?php
        }
    }
?>