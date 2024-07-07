<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link rel="stylesheet" href="calendar.css">
    </head>
    <body>
        <?php
            date_default_timezone_set("Asia/Taipei");
            $time=date("Y-m-d");
            $montharray=[1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December"];
            if(isset($_GET["month"])){
                $monthnum=(int)$_GET["month"];
                $month=$montharray[$monthnum];
                $year=$_GET["year"];
            }else{
                $monthnum=date("m");
                $month=date("F");
                $year=date("Y");
            }
            if(isset($_GET["submit"])){
                if($_GET["key"]=="1"){
                    if($_GET["nowmonth"]<12){
                        $monthnum=(int)$_GET["nowmonth"]+1;
                        $year=(int)$_GET["nowyear"];
                    }else{
                        $monthnum=1;
                        $year=(int)$_GET["nowyear"]+1;
                    }
                }else{
                    if($_GET["nowmonth"]>1){
                        $monthnum=(int)$_GET["nowmonth"]-1;
                        $year=(int)$_GET["nowyear"];
                    }else{
                        $monthnum=12;
                        $year=(int)$_GET["nowyear"]-1;
                    }
                }
                $month=$montharray[$monthnum];
            }
        ?>
        <div class="custom-calendar-wrap">
            <div class="custom-inner">
                <div class="custom-header clearfix">
                    <nav>
                        <a href="index.php?key=0&nowmonth=<?php echo($monthnum); ?>&nowyear=<?php echo($year); ?>&submit=" class="custom-btn custom-prev"></a>
                        <a href="index.php?key=1&nowmonth=<?php echo($monthnum); ?>&nowyear=<?php echo($year); ?>&submit=" class="custom-btn custom-next"></a>
                    </nav>
                    <h2 id="custom-month" class="custom-month"><?php echo($month) ?></h2>
                    <h3 id="custom-year" class="custom-year"><?php echo($year) ?></h3>
                </div>
                <div id="calendar" class="fc-calendar-container">
                    <div class="fc-calendar fc-five-rows">
                        <div class="fc-head">
                            <div>Sun</div>
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                        </div>
                        <div class="fc-body">
                            <?php
                                // 获取特定年月的第一天和最后一天
                                $firstday=date("Y-m-01",strtotime("$year-$month"));
                                $lastday=date("Y-m-t", strtotime("$year-$month"));

                                // 循环遍历每一天
                                $firstdayweeknumber=date("N",strtotime($firstday));
                                if($firstdayweeknumber==7){ $firstdayweeknumber=0; }
                                $lastdayweeknumber=date("N",strtotime($lastday))+1;
                                if($lastdayweeknumber==8){ $lastdayweeknumber=1; }
                                for($i=$firstday;$i<=$lastday;$i=date("Y-m-d",strtotime($i."+1 day"))){
                                    $day=(int)(explode("-",$i)[2]);
                                    $weeknumber=date("N",strtotime($i));
                                    if($day==1||$weeknumber%7==0){
                                        ?><div class="fc-row"><?php
                                    }
                                    if($day==1){
                                        for($j=$firstdayweeknumber;$j>0;$j=$j-1){
                                            ?><div><span class="fc-date"></span></div><?php
                                        }
                                    }
                                    if($time==$i){
                                        if($weeknumber%7==0){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==1){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==2){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==3){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==4){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==5){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==6){ ?><div class="fc-today"><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                    }else{
                                        if($weeknumber%7==0){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==1){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==2){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==3){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==4){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==5){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                        elseif($weeknumber%7==6){ ?><div><span class="fc-date"><?php echo($day); ?></span></div><?php }
                                    }
                                    if($day==(int)(explode("-",$lastday)[2])){
                                        for($j=$lastdayweeknumber;$j<7;$j=$j+1){
                                            ?><div><span class="fc-date"></span></div><?php
                                        }
                                    }
                                    if($day==(int)(explode("-",$lastday)[2])||$weeknumber%7==6){
                                        ?></div><?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>