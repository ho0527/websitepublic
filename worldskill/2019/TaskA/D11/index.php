<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>title</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/macossection.css">
        <script src="plugin/js/macossection.js"></script>
    </head>
    <body>
        <?php
            $data=json_decode(file_get_contents("data.json"),true);
            session_start();
            if(!isset($_SESSION["pagecount"])){ $_SESSION["pagecount"]=1; }
            if(!isset($_SESSION["orderkey"])){ $_SESSION["orderkey"]="name"; }
            if(!isset($_SESSION["ordertype"])){ $_SESSION["ordertype"]="asc"; }
        ?>
        <div class="macossectiondivy">
            <table class="maintable">
                <?php
                    if($_SESSION["orderkey"]=="name"){
                        if($_SESSION["ordertype"]=="asc"){
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=desc'" value="name ▲"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name ▼"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }
                    }elseif($_SESSION["orderkey"]=="gender"){
                        if($_SESSION["ordertype"]=="asc"){
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=desc'" value="gender ▲"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender ▼"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }
                    }elseif($_SESSION["orderkey"]=="age"){
                        if($_SESSION["ordertype"]=="asc"){
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=desc'" value="age ▲"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age ▼"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }
                    }elseif($_SESSION["orderkey"]=="company"){
                        if($_SESSION["ordertype"]=="asc"){
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=desc'" value="company ▲"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company ▼"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }
                    }elseif($_SESSION["orderkey"]=="email"){
                        if($_SESSION["ordertype"]=="asc"){
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=desc'" value="email ▲"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email ▼"></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone "></td>
                            </tr>
                            <?php
                        }
                    }elseif($_SESSION["orderkey"]=="phone"){
                        if($_SESSION["ordertype"]=="asc"){
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=desc'" value="phone ▲"></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=name&ordertype=asc'" value="name "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=gender&ordertype=asc'" value="gender "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=age&ordertype=asc'" value="age "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=company&ordertype=asc'" value="company "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=email&ordertype=asc'" value="email "></td>
                                <td class="td"><input type="button" class="oderbutton" onclick="location.href='?orderkey=phone&ordertype=asc'" value="phone ▼"></td>
                            </tr>
                            <?php
                        }
                    }else{
                        $_SESSION["orderkey"]="name";
                        ?><script>location.href="index.php"</script><?php
                    }
                ?>
                <?php
                    $pagedatacount=5;
                    if(count($data)<=$pagedatacount){ $rowcount=count($data); }
                    $pagecount=count($data)/$pagedatacount;
                    $modpagecount=count($data)%$pagedatacount;
                    $maxpagecount=ceil(count($data)/$pagedatacount);
                    if(isset($_GET["key"])){
                        $key=$_GET["key"];
                        if($key=="prev"){
                            if($_SESSION["pagecount"]>1){
                                $start=($_SESSION["pagecount"]-1)*$pagedatacount-$pagedatacount;
                                $rowcount=($_SESSION["pagecount"]-1)*$pagedatacount;
                                if($_SESSION["pagecount"]-1==$maxpagecount){ $rowcount=count($data); }
                                $_SESSION["pagecount"]=$_SESSION["pagecount"]-1;
                            }else{
                                $start=0;
                                $rowcount=$pagedatacount;
                                if(count($data)<=$pagedatacount){ $rowcount=count($data); }
                                $_SESSION["pagecount"]=1;
                            }
                        }elseif($key=="next"){
                            if($_SESSION["pagecount"]<$maxpagecount){
                                $start=$_SESSION["pagecount"]*$pagedatacount;
                                $rowcount=($_SESSION["pagecount"]+1)*$pagedatacount;
                                if($_SESSION["pagecount"]+1==$maxpagecount){ $rowcount=(floor($pagecount)*$pagedatacount)+$modpagecount; }
                                $_SESSION["pagecount"]=$_SESSION["pagecount"]+1;
                            }else{
                                $start=floor($pagecount)*$pagedatacount;
                                $rowcount=(floor($pagecount)*$pagedatacount)+$modpagecount;
                                $_SESSION["pagecount"]=$maxpagecount;
                            }
                        }else{ ?><script>alert("[ERROR] key type error");location.href="index.php"</script><?php }
                    }else{
                        $page=$_SESSION["pagecount"];
                        if($_SESSION["pagecount"]>1){
                            $start=$_SESSION["pagecount"]*$pagedatacount-$pagedatacount;
                            $rowcount=$_SESSION["pagecount"]*$pagedatacount;
                            if($_SESSION["pagecount"]==$maxpagecount){ $rowcount=count($data); }
                        }elseif($_SESSION["pagecount"]>=$maxpagecount){
                            $start=floor($pagecount)*$pagedatacount;
                            $rowcount=(floor($pagecount)*$pagedatacount)+$modpagecount;
                            $_SESSION["pagecount"]=$maxpagecount;
                        }else{
                            $start=0;
                            $rowcount=$pagedatacount;
                            if(count($data)<=$pagedatacount){ $rowcount=count($data); }
                            $_SESSION["pagecount"]=1;
                        }
                    }
                    if($_SESSION["ordertype"]=="asc"){
                        usort($data,function($a,$b){ return $a[$_SESSION["orderkey"]]>$b[$_SESSION["orderkey"]]; });
                    }else{
                        usort($data,function($a,$b){ return $a[$_SESSION["orderkey"]]<$b[$_SESSION["orderkey"]]; });
                    }
                    for($i=$start;$i<$rowcount;$i=$i+1){
                        ?>
                        <tr>
                            <td class="td"><?php echo($data[$i]["name"]); ?></td>
                            <td class="td"><?php echo($data[$i]["gender"]); ?></td>
                            <td class="td"><?php echo($data[$i]["age"]); ?></td>
                            <td class="td"><?php echo($data[$i]["company"]); ?></td>
                            <td class="td"><?php echo($data[$i]["email"]); ?></td>
                            <td class="td"><?php echo($data[$i]["phone"]); ?></td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>
        <div class="pagediv">
            <?php
                if($maxpagecount>5){
                    if($_SESSION["pagecount"]==$maxpagecount||$_SESSION["pagecount"]==$maxpagecount-1){
                        ?>
                        <input type="button" class="pagebutton" onclick="location.href='?key=prev'" value="<">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($maxpagecount-4); ?>'" value="<?php echo($maxpagecount-4); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($maxpagecount-3); ?>'" value="<?php echo($maxpagecount-3); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($maxpagecount-2); ?>'" value="<?php echo($maxpagecount-2); ?>">
                        <?php
                        if($_SESSION["pagecount"]==$maxpagecount-1){
                            ?>
                            <input type="button" class="page selectpage" onclick="location.href='?pagecount=<?php echo($maxpagecount-1); ?>'" value="<?php echo($maxpagecount-1); ?>">
                            <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($maxpagecount); ?>'" value="<?php echo($maxpagecount); ?>">
                            <input type="button" class="pagebutton" onclick="location.href='?key=next'" value=">">
                            <?php
                        }else{
                            ?>
                            <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($maxpagecount-1); ?>'" value="<?php echo($maxpagecount-1); ?>">
                            <input type="button" class="page selectpage" onclick="location.href='?pagecount=<?php echo($maxpagecount); ?>'" value="<?php echo($maxpagecount); ?>">
                            <input type="button" class="pagebuttondiabled" value=">" disabled>
                            <?php
                        }
                    }elseif($_SESSION["pagecount"]==1||$_SESSION["pagecount"]==2){
                        if($_SESSION["pagecount"]==1){
                            ?>
                            <input type="button" class="pagebuttondiabled" value="<" disabled>
                            <input type="button" class="page selectpage" onclick="location.href='?pagecount=<?php echo(1); ?>'" value="<?php echo(1); ?>">
                            <input type="button" class="page" onclick="location.href='?pagecount=<?php echo(2); ?>'" value="<?php echo(2); ?>">
                            <?php
                        }else{
                            ?>
                            <input type="button" class="pagebutton" onclick="location.href='?key=prev'" value="<">
                            <input type="button" class="page" onclick="location.href='?pagecount=<?php echo(1); ?>'" value="<?php echo(1); ?>">
                            <input type="button" class="page selectpage" onclick="location.href='?pagecount=<?php echo(2); ?>'" value="<?php echo(2); ?>">
                            <?php
                        }
                        ?>
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo(3); ?>'" value="<?php echo(3); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo(4); ?>'" value="<?php echo(4); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo(5); ?>'" value="<?php echo(5); ?>">
                        <input type="button" class="pagebutton" onclick="location.href='?key=next'" value=">">
                        <?php
                    }else{
                        ?>
                        <input type="button" class="pagebutton" onclick="location.href='?key=prev'" value="<">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($_SESSION['pagecount']-2); ?>'" value="<?php echo($_SESSION["pagecount"]-2); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($_SESSION['pagecount']-1); ?>'" value="<?php echo($_SESSION["pagecount"]-1); ?>">
                        <input type="button" class="page selectpage" onclick="location.href='?pagecount=<?php echo($_SESSION['pagecount']); ?>'" value="<?php echo($_SESSION["pagecount"]); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($_SESSION['pagecount']+1); ?>'" value="<?php echo($_SESSION["pagecount"]+1); ?>">
                        <input type="button" class="page" onclick="location.href='?pagecount=<?php echo($_SESSION['pagecount']+2); ?>'" value="<?php echo($_SESSION["pagecount"]+2); ?>">
                        <input type="button" class="pagebutton" onclick="location.href='?key=next'" value=">">
                        <?php
                    }
                }else{
                    ?><input type="button" class="pagebuttondiabled" value="<" disabled><?php
                    for($i=0;$i<$maxpagecount;$i=$i+1){
                        ?>
                        <input type="button" class="page" value="<?php echo($i+1); ?>">
                        <?php
                    }
                    ?><input type="button" class="pagebuttondiabled" value=">" disabled><?php
                }

                if(isset($_GET["pagecount"])){
                    $_SESSION["pagecount"]=$_GET["pagecount"];
                    ?><script>location.href="index.php"</script><?php
                }

                if(isset($_GET["orderkey"])&&isset($_GET["ordertype"])){
                    $_SESSION["orderkey"]=$_GET["orderkey"];
                    $_SESSION["ordertype"]=$_GET["ordertype"];
                    ?><script>location.href="index.php"</script><?php
                }
            ?>
        </div>
    </body>
</html>