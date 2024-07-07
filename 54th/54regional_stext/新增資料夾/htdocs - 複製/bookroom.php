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
        <?php include("link.php"); ?>
        <div class="nav" id="nav">
            <div class="title">訪客訂房 - <span id="titletext">選擇訂房資訊</span></div>
        </div>

        <div class="maincard" id="main">
            <div class="calendar" id="calendar">
                <?php
                    if(isset($_GET["year"])&&isset($_GET["month"])){
                        $year=(int)$_GET["year"];
                        $month=((int)$_GET["month"]>=12)?(12):(((int)$_GET["month"]<=1)?(1):((int)$_GET["month"]));
                    }else{
                        $year=date("Y");
                        $month=date("n");
                    }
    
                    $firstdaymonth=date("w",strtotime("$year-$month-01"));
                    $totalday=date("t",strtotime("$year-$month-01"));
                ?>
                <div class="year">
                    <input type="button" class="button" id="prevmonth" onclick="location.href='?year=<?= ($month-1<1)?($year-1):($year) ?>&month=<?= ($month-1<1)?(12):($month-1) ?>'" value="上一個月">
                    <div class="calendartitle"><?= $year ?>年<?= padstart($month,2) ?>月</div>
                    <input type="button" class="button" id="nextmonth" onclick="location.href='?year=<?= ($month+1>12)?($year+1):($year) ?>&month=<?= ($month+1>12)?(1):($month+1) ?>'" value="下一個月">
                </div>
                <div class="calendartable">
                    <div>日</div>
                    <div>一</div>
                    <div>二</div>
                    <div>三</div>
                    <div>四</div>
                    <div>五</div>
                    <div>六</div>
                    <?php
                        $date=1;
                        for($i=0;$i<42;$i=$i+1){
                            if($i<$firstdaymonth||$totalday<$date){
                                ?><div class="calendartd"></div><?php
                            }else{
                                $day=padstart($year,4)."/".padstart($month,2)."/".padstart($date,2);
                                $row=query($db,"SELECT * FROM `roomorder` WHERE (`startdate`<=? AND ?<=`enddate`) AND `delete`='false'",[$day,$day]);
                                $dataleftroom=[1,2,3,4,5,6,7,8];
                                
                                for($j=0;$j<count($row);$j=$j+1){
                                    $bookroomlist=explode(",",$row[$j]["roomno"]);
                                    for($k=0;$k<count($bookroomlist);$k=$k+1){
                                        unset($dataleftroom[$bookroomlist[$k]-1]);
                                    }
                                }

                                $dataleftroom=array_values($dataleftroom);                                    
                                ?>
                                <div class="calendartd calendardate" id="date_<?= $date ?>" data-day="<?= $day ?>" data-date="<?= $date ?>" data-leftroom="<?= implode(",",$dataleftroom) ?>">
                                    <?= $date ?><br>
                                    $5000<br>
                                    <?= count($dataleftroom) ?>間
                                </div>
                                <?php
                                $date=$date+1;
                            }
                        }
                    ?>
                </div>
            </div>
    
            <div class="bookroomform">
                訂房間數 <select id="roomcount">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                </select><br><br>
                入住天數<input type="text" class="notext" id="datecount" value="0" disabled><br><br>
                開始時間<input type="text" class="notext" id="startdate" value="請選擇日期" disabled><br><br>
                結束時間<input type="text" class="notext" id="enddate" value="請選擇日期" disabled><br><br>
                房間號碼<input type="text" class="notext" id="roomno" value="請選擇日期" disabled><br><br>
                <div class="textcenter">
                    <input type="button" class="btn button" id="autoselect" onclick="randomroom()" value="自動選擇房間">
                    <input type="button" class="btn button" id="selectroom" value="選擇房號"><br><br>
                    <input type="button" class="btn button" onclick="location.reload()" value="取消">
                    <input type="button" class="btn btn-warning" id="submit" value="確定訂房">
                </div>
            </div>
        </div>

        <div class="main pcenter textcenter displaynone" id="selectroomdiv"></div>

        <div class="main pcenter displaynone" id="check"></div>

        <script src="init.js"></script>
        <script src="bookroom.js"></script>
    </body>
</html>