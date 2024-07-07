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
        ?>
        <div class="nav tr" id="nav">
        </div>
        <div class="nav2 tc">
            訪客訂房 - 選擇訂房資訊
        </div>

        <div class="commentmain">
            <div class="cander">
                <div class="tc">
                    <input type="button" value="上一個月(沒做)" disabled>
                    2024年3月
                    <input type="button" value="下一個月(沒做)" disabled>
                </div>
                <?php
                    // $year=date("Y");
                    // $month=date("m");

                    // if(isset($_GET["year"])){
                    //     $year=(int)$_GET["year"];
                    //     $month=(int)$_GET["month"];
                    // }

                    // if($month-1<1){
                    //     $prevyear=$year-1;
                    //     $prevmonth=12;
                    // }else{
                    //     $prevyear=$year;
                    //     $prevmonth=$month-1;
                    // }

                    // if($month+1>12){
                    //     $nextyear=$year+1;
                    //     $nextmonth=1;
                    // }else{
                    //     $nextyear=$year;
                    //     $nextmonth=$month+1;
                    // }

                    // $firstmonth=date("a",time());
                    // $firstmonth=date("t",$year-$month-1);
                    // print($firstmonth);
                ?>
                <table class="table tc" id="table">
                    <tr>
                        <th>日</th>
                        <th>一</th>
                        <th>二</th>
                        <th>三</th>
                        <th>四</th>
                        <th>五</th>
                        <th>六</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="date" data-id="1">
                            1<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="2">
                            2<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                    </tr>
                    <tr>
                        <td class="date" data-id="3">
                            3<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="4">
                            4<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="5">
                            5<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="6">
                            6<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="7">
                            7<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="8">
                            8<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="9">
                            9<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                    </tr>
                    <tr>
                        <td class="date" data-id="10">
                            10<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="11">
                            11<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="12">
                            12<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="13">
                            13<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="14">
                            14<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="15">
                            15<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="16">
                            16<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                    </tr>
                    <tr>
                        <td class="date" data-id="17">
                            17<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="18">
                            18<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="19">
                            19<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="20">
                            20<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="21">
                            21<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="22">
                            22<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="23">
                            23<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                    </tr>
                    <tr>
                        <td class="date" data-id="24">
                            24<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="25">
                            25<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="26">
                            26<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="27">
                            27<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="28">
                            28<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="29">
                            29<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                        <td class="date" data-id="30">
                            30<br>
                            $5000<br>
                            還剩 8 間房
                        </td>
                    </tr>
                    <tr>
                        <td class="date" data-id="31">
                            31<br>
                            $5000<br>
                            還剩 0 間房
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="comment tc">
                房間數量: 1<br>
                住宿天數: <span id="sd">0</span><br>
                房號: <span id="room"></span><br>
                <input type="button" id="select" value="自動選擇房號">
                <input type="button" id="" value="選擇房號">
                <input type="button" onclick="location.reload()" value="取消">
                <input type="button" onclick="alert('已送出(應該吧');location.reload()" value="送出">
            </div>
        </div>

        <script src="init.js"></script>
        <script src="bookroom.js"></script>
    </body>
</html>