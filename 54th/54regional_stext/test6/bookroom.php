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
                        $year=padstart(2,(isset($_GET["year"]))?$_GET["year"]:date("Y"));
                        $month=padstart(2,(isset($_GET["month"]))?$_GET["month"]:date("m"));
                    ?>
                    <input type="button" class="btn btn-outline-dark" id="prevmonth" onclick="location.href='?year=<?= ($month-1<1)?($year-1):$year ?>&month=<?= ($month-1<1)?(12):($month-1) ?>'" value="前一個月">
                    <div><?= $year ?>年<?= $month ?>月</div>
                    <input type="button" class="btn btn-outline-dark" id="nextmonth" onclick="location.href='?year=<?= ($month+1>12)?($year+1):$year ?>&month=<?= ($month+1>12)?(1):($month+1) ?>'" value="下一個月">
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
                        $firsrdate=date("w",strtotime("$year-$month-1"));
                        $totalday=date("t",strtotime("$year-$month-1"));
                        $date=1;
                        for($i=1;$i<=42;$i=$i+1){
                            if($firsrdate<$i&&$date<=$totalday){
                                $day=$year."/".$month."/".padstart(2,$date);
                                $leftroom=[1,2,3,4,5,6,7,8];
                                $row=query("SELECT*FROM `bookroom` WHERE `firstday`<=? AND ?<=`lastday` AND `delete`=''",[$day,$day]);
                                for($j=0;$j<count($row);$j=$j+1){
                                    unset($leftroom[$row[$j]["room"]-1]);
                                }
                                $leftroom=array_values($leftroom);
                                ?><div class="cdiv datediv" id="<?= $date ?>" data-day="<?= $day ?>">
                                    <?= $date ?><br>
                                    $5000<br>
                                    <?= count($leftroom) ?>間
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
                <div class="div">訂房間數: 1</div>
                <div class="div">
                    入住天數
                    <div id="totaldate">請選擇日期</div>
                </div>
                <div class="div">
                    入住日期
                    <div id="firstday">請選擇日期</div>
                </div>
                <div class="div">
                    ~
                    <div id="lastday">請選擇日期</div>
                </div>
                <div class="div">
                    房號
                    <div id="room">請選擇日期</div>
                </div>
                <div class="div text-center">
                    <input type="button" class="btn btn-light" onclick="auto()" value="自動選擇房號">
                    <input type="button" class="btn btn-light" onclick="localStorage.setItem('book',JSON.stringify(book));localStorage.setItem('location',location.href);location.href='bookroomroom.php'" value="選擇房號">
                    <input type="button" class="btn btn-light" onclick="localStorage.removeItem('book');localStorage.removeItem('location');location.reload()" value="取消">
                    <input type="button" class="btn btn-warning" onclick="localStorage.setItem('book',JSON.stringify(book));localStorage.setItem('location',location.href);location.href='bookroomcheck.php'" value="確定訂房">
                </div>
            </div>
        </div>

        <script src="init.js"></script>
        <script src="bookroom.js"></script>
    </body>
</html>