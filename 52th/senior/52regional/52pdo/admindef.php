<?php
    function printrow($a){
        for($row=0;$row<count($a);$row=$row+1){
            if($a[$row]["username"]=="admin"||$a[$row]["usernumber"]=="null"){
                ?>
                    <tr>
                        <td class="admin-table-num" id=<?= $a[$row]["usernumber"]; ?>>
                            <?php print_r($a[$row]["usernumber"]); ?>
                            <input type="button" value="修改" disabled>
                            <button name="del" disabled>刪除帳號</button>
                        </td>
                        <td class="admin-table"><?php print_r($a[$row]["username"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["password"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["name"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["permission"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["logintime"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["logouttime"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["move"]); ?></td>
                        <td class="admin-table"><?php print_r($a[$row]["movetime"]); ?></td>
                    </tr>
                <?php
            }else{
                ?>
                <tr>
                    <td class="admin-table-num" id=<?= $a[$row]["usernumber"]; ?>>
                        <?php print_r($a[$row]["usernumber"]); ?>
                        <input type="button" value="修改" onclick="location.href='adminedit.php?number=<?= $a[$row]['usernumber'] ?>'">
                        <button name="del" value="<?= $a[$row]["usernumber"]; ?>">刪除帳號</button>
                    </td>
                    <td class="admin-table"><?php print_r($a[$row]["username"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["password"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["name"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["permission"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["logintime"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["logouttime"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["move"]); ?></td>
                    <td class="admin-table"><?php print_r($a[$row]["movetime"]); ?></td>
                </tr>
                <?php
            }
        }
    }

    function up($data,$comper){
        $a=$data;
        for($i=0;$i<count($a)-1;$i=$i+1){
            for($j=0;$j<count($a)-$i-1;$j=$j+1){
                if($a[$j][$comper]>$a[$j+1][$comper]){
                    $tamp=$a[$j];
                    $a[$j]=$a[$j+1];
                    $a[$j+1]=$tamp;
                }
            }
        }
        printrow($a);
    }

    function down($data,$comper){
        $a=$data;
        for($i=0;$i<count($a)-1;$i=$i+1){
            for($j=0;$j<count($a)-$i-1;$j=$j+1){
                if($a[$j][$comper]<$a[$j+1][$comper]){
                    $tamp=$a[$j];
                    $a[$j]=$a[$j+1];
                    $a[$j+1]=$tamp;
                }
            }
        }
        printrow($a);
    }

    function issetgetupdown($data){
        @$number=$_GET["num-up-down"];
        @$user=$_GET["user-up-down"];
        @$code=$_GET["code-up-down"];
        @$name=$_GET["name-up-down"];
        if($number=="升冪"){
            down($data,"usernumber");
            ?><script>document.getElementById("num-up-down").value="降冪"</script><?php
        }elseif($user=="升冪"){
            down($data,"username");
            ?><script>document.getElementById("user-up-down").value="降冪"</script><?php
        }elseif($code=="升冪"){
            down($data,"password");
            ?><script>document.getElementById("code-up-down").value="降冪"</script><?php
        }elseif($name=="升冪"){
            down($data,"name");
            ?><script>document.getElementById("name-up-down").value="降冪"</script><?php
        }elseif(isset($number)||isset($user)||isset($code)||isset($name)){
            header("location:adminWelcome.php");
        }else{
            up($data,"usernumber");
        }
    }
?>