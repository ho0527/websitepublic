<?php
    function up($data,$comper){
        $a=[];
        while($row=mysqli_fetch_row($data)){
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
            if($a[$row][2]=="admin"){
                ?>
                <tr>
                    <td class="admintablenum" id=<?= $a[$row][1]; ?>>
                        <?php print_r($a[$row][1]); ?>
                        <input type="button" value="修改" disabled>
                        <button name="del" disabled>刪除帳號</button>
                    </td>
                    <td class="admintable"><?php print_r($a[$row][2]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][3]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][4]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][5]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][6]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][7]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][8]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][9]); ?></td>
                </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="admintablenum" id=<?= $a[$row][1]; ?>>
                        <?php print_r($a[$row][1]); ?>
                        <input type="button" value="修改" onclick="location.href='adminedit.php?number=<?= $a[$row][1] ?>'">
                        <button name="del" value="<?= $a[$row][1]; ?>">刪除帳號</button>
                    </td>
                    <td class="admintable"><?php print_r($a[$row][2]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][3]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][4]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][5]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][6]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][7]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][8]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][9]); ?></td>
                </tr>
                <?php
            }
        }
    }

    function down($data,$comper){
        $a=[];
        while($row=mysqli_fetch_row($data)){
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
            if($a[$row][2]=="admin"){
                ?>
                <tr>
                    <td class="admintablenum" id=<?= $a[$row][1]; ?>>
                        <?php print_r($a[$row][1]); ?>
                        <input type="button" value="修改" disabled>
                        <button name="del" disabled>刪除帳號</button>
                    </td>
                    <td class="admintable"><?php print_r($a[$row][2]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][3]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][4]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][5]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][6]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][7]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][8]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][9]); ?></td>
                </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="admintablenum" id=<?= $a[$row][1]; ?>>
                        <?php print_r($a[$row][1]); ?>
                        <input type="button" value="修改" onclick="location.href='adminedit.php?number=<?= $a[$row][1] ?>'">
                        <button name="del" value="<?= $a[$row][1]; ?>">刪除帳號</button>
                    </td>
                    <td class="admintable"><?php print_r($a[$row][2]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][3]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][4]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][5]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][6]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][7]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][8]); ?></td>
                    <td class="admintable"><?php print_r($a[$row][9]); ?></td>
                </tr>
                <?php
            }
        }
    }
?>