<div class="macosmaindiv macossectiondiv">
    <table>
        <?php
            $questionrow=query($db,"SELECT*FROM `question` WHERE `id`='$id'");
            $count=count($questionrow);
            for($i=0;$i<count($questionrow);$i=$i+1){
                $question=[];
                if(@$questionrow[$i][6]!=""){
                    $question=explode("|&|",$questionrow[$i][6]);
                }
                for($j=count($question);$j<6;$j=$j+1){
                    $question[]="";
                }
                ?>
                <tr>
                    <?php
                    if(isset($questionrow[$i][5])&&$questionrow[$i][5]!="none"){
                        ?><td rowspan="2" class="order">
                            <?php if($questionrow[$i][4]=="true"){ ?><div class="required">*必填</div><?php } ?>
                            <input type="text" name="count<?php echo($i) ?>" class="count" value="<?php echo($questionrow[$i][2]) ?>" readonly>
                        </td><?php
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    if(!isset($questionrow[$i][5])||$questionrow[$i][5]=="none"){
                        $count=$count-1;
                    }else{
                        ?>
                        <td class="output" id="output<?= $i ?>">
                            <div class="questiondiv">
                                題目說明: <?php echo($questionrow[$i][3]) ?><br>
                                <?php
                                if($questionrow[$i][5]=="yesno"){
                                    ?>
                                    是<input type="radio" name="yesno" class="radio" value="yes">
                                    否<input type="radio" name="yesno" class="radio" value="no">
                                    <?php
                                }elseif($questionrow[$i][5]=="single"){
                                    for($j=0;$j<6;$j=$j+1){
                                        if(@$question[$j]!=""){
                                            ?><input type="radio" name="single<?php echo($i) ?>" class="radio" value="<?php echo($j+1) ?>"><?php
                                            echo($question[$j]." ");
                                        }
                                    }
                                }elseif($questionrow[$i][5]=="multi"){
                                    for($j=0;$j<6;$j=$j+1){
                                        if(@$question[$j]!=""){
                                            ?><input type="checkbox" name="<?php echo($i) ?>multi<?php echo($j) ?>" class="checkbox" value="<?php echo($j+1) ?>"><?php
                                            echo($question[$j]." ");
                                        }
                                    }
                                    ?><br>
                                    <?php
                                    if($questionrow[$i][7]==""||$questionrow[$i][7]=="true"){
                                        ?>其他:<input type="text" name="multiauther<?php echo($i) ?>" class="forminputlongtext"><?php
                                    }
                                }elseif($questionrow[$i][5]=="qa"){
                                    ?><textarea cols="30" rows="5" name="qa<?php echo($i) ?>" placeholder="問答題"></textarea><?php
                                }else{ ?><script>sql001();location.href="admin.php"</script><?php }
                                ?>
                            </div>
                        </td>
                        <?php
                    }
                ?></tr><?php
            }
        ?>
    </table>
    <?php
        if(isset($_POST["newqust"])){
            $_SESSION["count"]=$_SESSION["count"]+1;
            ?><script>location.href="form.php"</script><?php
        }
        if(isset($_POST["lestqust"])){
            $_SESSION["count"]=$_SESSION["count"]-1;
            ?><script>location.href="form.php"</script><?php
        }
        if(isset($_POST["save"])){
            for($i=0;$i<$count;$i=$i+1){
                $response="";
                $order=$_POST["count".$i];
                if(isset($_POST["required".$i])){ $required="true"; }else{ $required="false"; }
                $mod=$questionrow[$order][5];
                echo "\$mod ="; print_r($mod); echo "<br>";
                if($mod=="yesno"){
                    echo "\$in ="; print_r($mod); echo "<br>";
                    if(isset($_POST["yesno".$i])){
                        $response=$_POST["yesno".$i];
                    }
                }elseif($mod=="single"){
                    if(isset($_POST["single".$i])){
                        $response=$response.$_POST[$i."single"]."|&|";
                    }
                }elseif($mod=="multi"){
                    for($j=1;$j<=6;$j=$j+1){
                        if(isset($_POST[$i."multi".$j])){
                            $response=$response.$_POST[$i."multi".$j]."|&|";
                        }
                    }
                    $response=$response.$_POST["multiauther".$i];
                }elseif($mod=="qa"){
                    $response=$_POST["qa".$i];
                }else{}
                query($db,"INSERT INTO `response`(`questionid`,`questionorder`,`response`)VALUES('$id','$order',?)",[$response]);
            }
            $count=(int)$row[4]+1;
            query($db,"UPDATE `question` SET `responcount`='$count' WHERE `id`='$id'");
            ?><script>alert("儲存成功");location.href="index.php"</script><?php
        }
        if(isset($_POST["cancel"])){
            unset($_SESSION["id"]);
            unset($_SESSION["count"]);
            ?><script>location.href="index.php"</script><?php
        }
    ?>
</div>