<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nav" id="nav">
            <div class="title">訪客訂房 - 選擇訂房資訊</div>
        </div>

        <div class="maindiv">
            <div class="bookroomleft">
                <div class="flex">
                    <?php
                        $year=strpad(4,(isset($_GET["year"])?$_GET["year"]:date("Y")));
                        $month=strpad(2,(isset($_GET["month"])?$_GET["month"]:date("m")));
                    ?>
                    <input type="button" class="btn btn-outline-dark" id="prevmonth" onclick="location.href='?year=<?= $month-1<1?$year-1:$year ?>&month=<?= $month-1<1?12:$month-1 ?>'" value="前一個月">
                    <div><?= $year ?>年<?= $month ?>月</div>
                    <input type="button" class="btn btn-outline-dark" id="nextmonth" onclick="location.href='?year=<?= 12<$month+1?$year+1:$year ?>&month=<?= 12<$month+1?1:$month+1 ?>'" value="下一個月">
                </div>
                <div class="c text-center">
                    <div>日</div>
                    <div>一</div>
                    <div>二</div>
                    <div>三</div>
                    <div>四</div>
                    <div>五</div>
                    <div>六</div>
                    <?php
                        $firstdate=date("w",strtotime("$year-$month-1"));
                        $totaldate=date("t",strtotime("$year-$month-1"));
                        $date=1;
                        for($i=1;$i<=42;$i=$i+1){
                            if($firstdate<$i&&$date<=$totaldate){
                                $day=$year."-".$month."-".strpad(2,$date);
                                $cost=(($date+$firstdate)%7==6||($date+$firstdate)%7==0)?"7500":"5000";
                                $leftroom=[1,2,3,4,5,6,7,8];
                                $row=query("SELECT*FROM `bookroom` WHERE (`startday`<=? AND ?<=`endday`)AND `delete`=''",[$day,$day]);
                                for($j=0;$j<count($row);$j=$j+1){
                                    $room=explode(",",$row[$j]["room"]);
                                    for($k=0;$k<count($room);$k=$k+1){
                                        unset($leftroom[$room[$k]-1]);
                                    }
                                }
                                $leftroom=array_values($leftroom);
                                ?><div class="cdiv cdatediv" id="<?= (int)((date("U",strtotime("$day"))+8*24)/(60*60*24)) ?>" data-day="<?= $day ?>" data-cost="<?= $cost ?>">
                                    <?= $date ?><br>
                                    <?= count($leftroom) ?>間<br>
                                    <?= $cost ?>
                                </div><?php
                                $date=$date+1;
                            }else{
                                ?><div class="cdiv"></div><?php
                            }
                        }
                    ?>
                </div>
            </div>
            <div class="bookroomright">
                <div class="div">
                    訂房間數
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
                </div>
                <div class="div">
                    入住天數
                    <div id="totalday">請先選擇日期</div>
                </div>
                <div class="div">
                    入住日期
                    <div id="startday">請先選擇日期</div>
                    ~
                    <div id="endday">請先選擇日期</div>
                </div>
                <div class="div">
                    房號
                    <div id="room">請先選擇日期</div>
                </div>
                <div class="div text-center">
                    <input type="button" class="btn btn-light" onclick="auto()" value="自動選擇房號">
                    <input type="button" class="btn btn-light" onclick="localStorage.setItem('book',JSON.stringify(book));localStorage.setItem('location',location.href);location.href='bookroomroom.php'" value="選擇房號">
                    <input type="button" class="btn btn-light" onclick="localStorage.removeItem('book');localStorage.removeItem('location');location.href='bookroom.php'" value="取消">
                    <input type="button" class="btn btn-warning" onclick="localStorage.setItem('book',JSON.stringify(book));localStorage.setItem('location',location.href);location.href='bookroomcheck.php'" value="確定訂房">
                </div>
            </div>
        </div>

        <script src="init.js"></script>
        <script src="bookroom.js"></script>
    </body>
</html>