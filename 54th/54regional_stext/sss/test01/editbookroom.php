<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_GET["id"])){
                $_SESSION["id"]=$_GET["id"];
            }
            $id=$_SESSION["id"];
            $datarow=query("SELECT*FROM `bookroom` WHERE `id`=?",[$id])[0];
        ?>
        <div class="nav" id="nav">
            <div class="title">修改訂房 - <span id="title2">選擇訂房資訊</span></div>
        </div>

        <div class="maincard" id="main">
            <div class="bookroomleft">
                <?php
                    $year=isset($_GET["year"])?("1970"<=$_GET["year"]?$_GET["year"]:"1970"):date("Y");
                    $month=isset($_GET["month"])?(1<=$_GET["month"]?12>=$_GET["month"]?$_GET["month"]:12:1):date("m");
                ?>
                <div class="flex">
                    <input type="button" class="btn btn-outline-dark" onclick="location.href='?year=<?= $month-1<1?$year-1:$year; ?>&month=<?= $month-1<1?12:$month-1; ?>'" value="前一個月">
                    <div><?= padstart(4,$year) ?>年<?= padstart(2,$month) ?>月</div>
                    <input type="button" class="btn btn-outline-dark" onclick="location.href='?year=<?= $month+1>12?$year+1:$year; ?>&month=<?= $month+1>12?1:$month+1; ?>'" value="下一個月">
                </div>
                <div class="c">
                    <div>日</div>
                    <div>一</div>
                    <div>二</div>
                    <div>三</div>
                    <div>四</div>
                    <div>五</div>
                    <div>六</div>
                    <?php
                        $f=date("w",strtotime("$year-$month-1"));
                        for($i=1;$i<=42;$i=$i+1){
                            if($f<$i&&$i-$f<=date("t",strtotime("$year-$month-1"))){
                                $day=padstart(4,$year)."/".padstart(2,$month)."/".padstart(2,$i-$f);
                                $row=query("SELECT*FROM `bookroom` WHERE (`startday`<=?AND?<=`endday`)AND`delete`=''AND`id`!=?",[$day,$day,$id]);
                                $leftroom=[1,2,3,4,5,6,7,8];
                                for($j=0;$j<count($row);$j=$j+1){
                                    $room=explode(",",$row[$j]["room"]);
                                    for($k=0;$k<count($room);$k=$k+1){
                                        unset($leftroom[$room[$k]-1]);
                                    }
                                }
                                ?><div class="bookroomleftdiv center bookroomdate" id="<?= (date("U",strtotime($day))+8*3600)/(24*60*60); ?>" data-day="<?= $day; ?>">
                                    <?= $i-$f ?><br>
                                    $5000<br>
                                    <?= count(array_values($leftroom)); ?>間
                                </div><?php
                            }else{
                                ?><div class="bookroomleftdiv center"></div><?php
                            }
                        }
                    ?>
                </div>
            </div>
            <div>
                訂房間數: 
                <select id="roomcount">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                </select>
                <br><br>
                <div class="margin-5px0px">
                    入住天數
                    <div id="totaldate">請選擇日期</div>
                </div><br>
                <div class="margin-5px0px">
                    入住日期
                    <div id="startday">請選擇日期</div>
                </div>
                <div class="margin-5px0px">
                    ~
                    <div id="endday">請選擇日期</div>
                </div><br>
                <div class="margin-5px0px">
                    房號
                    <div id="room">請選擇日期</div>
                </div><br>
                <div class="text-center">
                    <input type="button" class="btn btn-light" onclick="auto()" value="自動產生房號">
                    <input type="button" class="btn btn-light" id="select" value="選擇房號">
                    <input type="button" class="btn btn-light" onclick="localStorage.removeItem('book');localStorage.setItem('reload','true');location.reload()" value="取消">
                    <input type="button" class="btn btn-warning" id="submit" value="確定訂房">
                </div>
            </div>
        </div>
        <div class="maindiv displaynone" id="main2"></div>

        <script src="init.js"></script>
        <script>
            let id=<?= $id; ?>;
            let book={
                "roomcount": <?= count(explode(",",$datarow[2])); ?>,
                "totaldate": 0,
                "totalprice": 0,
                "startdate": <?= (date("U",strtotime($datarow["startday"])+8*3600)/(24*60*60)); ?>,
                "enddate": <?= (date("U",strtotime($datarow["endday"])+8*3600)/(24*60*60)); ?>,
                "startday": <?= json_encode($datarow["startday"]); ?>,
                "endday": <?= json_encode($datarow["endday"]); ?>,
                "canbookroom": <?= json_encode(array_values($leftroom)) ?>,
                "bookroom": <?= json_encode(explode(",",$datarow["room"])); ?>,
            }
        </script>
        <script src="editbookroom.js"></script>
    </body>
</html>