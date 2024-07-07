<?php
    function uper($todo){
        while($row=mysqli_fetch_row($todo)){
            $start_time=substr($row[3],0,2)*60+substr($row[3],3,5);
            $hr=substr($row[4],0,2)-substr($row[3],0,2);
            $min=substr($row[4],3,5)-substr($row[3],3,5);
            ?>
            <form>
                <div class="todobox" id="upbox<?= $row[0]; ?>" draggable="true" value="<?= $row[0]; ?>" style="height:<?= ($hr*30)+(($min/30)*15); ?>px;top:<?= 145+($start_time/2)+35; ?>px;left:180px;">
                    <button type="submit" name="edit" value="<?= $row[0]; ?>">edit</button>
                    <button type="submit" name="preview" value="<?= $row[0]; ?>">預覽</button>
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
                <div class="todobox" id="downbox<?= $row[0]; ?>" draggable="true" value="<?= $row[0]; ?>" style="height:<?= ($hr*30)+(($min/30)*15); ?>px;bottom:<?= (($end_time)/2)+65;?>px;left:180px;">
                    <button type="submit" name="edit" value="<?= $row[0]; ?>">edit</button>
                    <button type="submit" name="preview" value="<?= $row[0]; ?>">預覽</button>
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

?>