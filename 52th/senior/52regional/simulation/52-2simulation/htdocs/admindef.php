<?php
    function uper($db,$data2){
        $data=mysqli_query($db,"SELECT*FROM `data`");
        $a=[];
        while($row=mysqli_fetch_row($data)){
            array_push($a,$row);
        }
        for($i=0;$i<count($a);$i=$i+1){
            for($j=0;$j<count($a)-$i-1;$j=$j+1){
                if($a[$j][$data2]>$a[$j+1][$data2]){
                    $temp=$a[$j];
                    $a[$j]=$a[$j+1];
                    $a[$j+1]=$temp;
                }
            }
        }
        for($i=0;$i<count($a);$i=$i+1){
            if($a[$i][2]=="admin"||$a[$i][1]=="null"){
                ?>
                <form>
                    <tr>
                        <td class="adminnum"><?= $a[$i][1] ?><button type="button" onclick="location.href='adminedit.php?val=<?= $a[$i][1] ?>'" value="<?= $a[$i][1] ?>" disabled>編輯</button><button type="submit" name="del" value="<?= $a[$i][1] ?>" disabled>刪除帳號</button></td>
                        <td class="admin"><?= $a[$i][2] ?></td>
                        <td class="admin"><?= $a[$i][3] ?></td>
                        <td class="admin"><?= $a[$i][4] ?></td>
                        <td class="admin"><?= $a[$i][5] ?></td>
                        <td class="admin"><?= $a[$i][6] ?></td>
                        <td class="admin"><?= $a[$i][7] ?></td>
                        <td class="admin"><?= $a[$i][8] ?></td>
                        <td class="admin"><?= $a[$i][9] ?></td>
                    </tr>
                </form>
                <?php
            }else{
                ?>
                <form>
                    <tr>
                        <td class="adminnum"><?= $a[$i][1] ?><button type="button" onclick="location.href='adminedit.php?val=<?= $a[$i][1] ?>'" value="<?= $a[$i][1] ?>">編輯</button><button type="submit" name="del"value="<?= $a[$i][1] ?>">刪除帳號</button></td>
                        <td class="admin"><?= $a[$i][2] ?></td>
                        <td class="admin"><?= $a[$i][3] ?></td>
                        <td class="admin"><?= $a[$i][4] ?></td>
                        <td class="admin"><?= $a[$i][5] ?></td>
                        <td class="admin"><?= $a[$i][6] ?></td>
                        <td class="admin"><?= $a[$i][7] ?></td>
                        <td class="admin"><?= $a[$i][8] ?></td>
                        <td class="admin"><?= $a[$i][9] ?></td>
                    </tr>
                </form>
                <?php
            }
        }
    }
    function lower($db,$data2){
        $data=mysqli_query($db,"SELECT*FROM `data`");
        $a=[];
        while($row=mysqli_fetch_row($data)){
            array_push($a,$row);
        }
        for($i=0;$i<count($a);$i=$i+1){
            for($j=0;$j<count($a)-$i-1;$j=$j+1){
                if($a[$j][$data2]>$a[$j+1][$data2]){
                    $temp=$a[$j];
                    $a[$j]=$a[$j+1];
                    $a[$j+1]=$temp;
                }
            }
        }
        for($i=0;$i<count($a);$i=$i+1){
            if($a[$i][2]=="admin"||$a[$i][1]=="null"){
                ?>
                <form>
                    <tr>
                        <td class="adminnum"><?= $a[$i][1] ?><button type="button" onclick="location.href='adminedit.php?val=<?= $a[$i][1] ?>'" value="<?= $a[$i][1] ?>" disabled>編輯</button><button type="submit" name="del" value="<?= $a[$i][1] ?>" disabled>刪除帳號</button></td>
                        <td class="admin"><?= $a[$i][2] ?></td>
                        <td class="admin"><?= $a[$i][3] ?></td>
                        <td class="admin"><?= $a[$i][4] ?></td>
                        <td class="admin"><?= $a[$i][5] ?></td>
                        <td class="admin"><?= $a[$i][6] ?></td>
                        <td class="admin"><?= $a[$i][7] ?></td>
                        <td class="admin"><?= $a[$i][8] ?></td>
                        <td class="admin"><?= $a[$i][9] ?></td>
                    </tr>
                </form>
                <?php
            }else{
                ?>
                <form>
                    <tr>
                        <td class="adminnum"><?= $a[$i][1] ?><button type="button" onclick="location.href='adminedit.php?val=<?= $a[$i][1] ?>'" value="<?= $a[$i][1] ?>">編輯</button><button type="submit" name="del"value="<?= $a[$i][1] ?>">刪除帳號</button></td>
                        <td class="admin"><?= $a[$i][2] ?></td>
                        <td class="admin"><?= $a[$i][3] ?></td>
                        <td class="admin"><?= $a[$i][4] ?></td>
                        <td class="admin"><?= $a[$i][5] ?></td>
                        <td class="admin"><?= $a[$i][6] ?></td>
                        <td class="admin"><?= $a[$i][7] ?></td>
                        <td class="admin"><?= $a[$i][8] ?></td>
                        <td class="admin"><?= $a[$i][9] ?></td>
                    </tr>
                </form>
                <?php
            }
        }
    }
?>