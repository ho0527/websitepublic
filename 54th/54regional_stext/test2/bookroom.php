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
        <div class="nav tr" id="nav"></div>
        <div class="nav2" id="">訪客訂房 - <span id="title">選擇訂房資訊</span></div>

        <div class="top100px bookroomdiv">
            <div class="bookroomright" id="bookroomright">
                <?php
                    $year=date("Y");
                    $month=date("m");

                    if(isset($_GET["year"])){
                        $year=$_GET["year"];
                    }

                    if(isset($_GET["month"])){
                        $month=$_GET["month"];
                    }

                    $year=(int)$year;
                    $month=(int)$month;

                    if($month-1<1){
                        $prevyear=$year-1;
                        $prevmonth=12;
                    }else{
                        $prevyear=$year;
                        $prevmonth=$month-1;
                    }

                    if($month+1>12){
                        $nextyear=$year+1;
                        $nextmonth=1;
                    }else{
                        $nextyear=$year;
                        $nextmonth=$month+1;
                    }

                    $fm=date("w",strtotime("$year-$month-1"));
                    $td=date("t",strtotime("$year-$month-1"));
                ?>
                <div class="flex">
                    <input type="button" onclick="location.href='?year=<?= $prevyear ?>&month=<?= $prevmonth ?>'" value="前一個月">
                    <div><?= $year ?>年<?= $month ?>月</div>
                    <input type="button" onclick="location.href='?year=<?= $nextyear ?>&month=<?= $nextmonth ?>'" value="下一個月">
                </div>
                <table>
                    <tr>
                        <th class="td">日</th>
                        <th class="td">一</th>
                        <th class="td">二</th>
                        <th class="td">三</th>
                        <th class="td">四</th>
                        <th class="td">五</th>
                        <th class="td">六</th>
                    </tr>
                    <?php
                        for($i=0;$i<$fm;$i=$i+1){
                            ?><td class="td"></td><?php
                        }
                        for($i=0;$i<$td;$i=$i+1){
                            $leftroom=[];
                            if(!isset($_SESSION["key1"])&&$i+1==20&&$year==2024&&$month==2){
                                $roomleft=7;
                                $leftroom=[1,2,3,4,5,6,7];
                                $notleftroom=[8];
                            }elseif(!isset($_SESSION["key2"])&&$i+1==10&&$year==2024&&$month==2){
                                $roomleft=0;
                                $leftroom=[];
                                $notleftroom=[1,2,3,4,5,6,7,8];
                            }elseif(!isset($_SESSION["key3"])&&$i+1==6&&$year==2024&&$month==2){
                                $roomleft=7;
                                $leftroom=[1,2,3,4,5,6,8];
                                $notleftroom=[7];
                            }elseif(!isset($_SESSION["key3"])&&$i+1==7&&$year==2024&&$month==2){
                                $roomleft=7;
                                $leftroom=[1,2,3,4,5,6,8];
                                $notleftroom=[7];
                            }else{
                                $roomleft=8;
                                $leftroom=[1,2,3,4,5,6,7,8];
                                $notleftroom=[];
                            }
                            if(($i+$fm)%7==0){
                                ?></tr><tr><?php
                            }
                            ?>
                            <td class="td date" id="date_<?= $i+1 ?>" data-year="<?= $year ?>" data-month="<?= $month ?>" data-day="<?= $i+1 ?>" data-leftroom="<?= implode(",",$leftroom) ?>" data-notleftroom="<?= implode(",",$notleftroom) ?>">
                                <?= $i+1 ?><br>
                                $5000<br>
                                還剩 <?= $roomleft ?> 間房
                            </td>
                            <?php
                        }
                    ?>
                </table>
            </div>
            <div class="bookroomleft" id="bookroomleft">
                訂房間數: <select name="" id="roomcount" disabled>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                </select><br>
                入住天數: <input type="text" class="noinput" id="daycount" disabled><br>
                入住日期: <input type="text" class="noinput" id="startday" disabled> ~ <input type="text" class="noinput" id="endday" disabled><br>
                房號: <input type="text" class="noinput" id="roomno" disabled><br>
                <div class="tc">
                    <input type="button" id="autoselect" value="自動產生房號">
                    <input type="button" id="select" value="選擇房號">
                    <input type="button" onclick="location.href='bookroom.php'" value="取消">
                    <input type="button" id="check1" value="確定訂房">
                </div>
            </div>
            <div id="selectdiv" style="display: none">
                <div class="tc" id="selecttitle"></div>
                <input type="button" class="selectbutton" id="Room1" data-id="1" value="Room1(空房)">
                <input type="button" class="selectbutton" id="Room2" data-id="2" value="Room2(空房)">
                <input type="button" class="selectbutton" id="Room3" data-id="3" value="Room3(空房)">
                <input type="button" class="selectbutton" id="Room4" data-id="4" value="Room4(空房)">
                <input type="button" class="selectbutton" id="Room5" data-id="5" value="Room5(空房)">
                <input type="button" class="selectbutton" id="Room6" data-id="6" value="Room6(空房)">
                <input type="button" class="selectbutton" id="Room7" data-id="7" value="Room7(空房)">
                <input type="button" class="selectbutton" id="Room8" data-id="8" value="Room8(空房)">
                <div class="tc">
                    <input type="button" id="finish" value="確定選取">
                    <input type="button" id="clear" value="取消">
                    <input type="button" id="back" value="返回">
                </div>
            </div>
            <div id="checkdiv" style="display: none">
            </div>
        </div>

        <script src="init.js"></script>
        <script src="bookroom.js"></script>
    </body>
</html>