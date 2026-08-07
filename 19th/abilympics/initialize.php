<?php
	$sitetitle="Web01芬蘭極光旅遊資訊平台";
	date_default_timezone_set("Asia/Taipei");
	$db=new PDO("mysql:host=localhost;dbname=19thabilympics;charset=utf8mb4","root","");
	$time=date("Y-m-d H:i:s");
	session_start();

	function query($db,$sql,$data=[]){
		$p=$db->prepare($sql);
		$p->execute($data);
		return $p->fetchAll();
	}

	//Email遮蔽 alvin@gmail.com => a***@gmail.com
	function maskemail($email){
		$at=strpos($email,"@");
		if($at===false){
			return $email;
		}
		return mb_substr($email,0,1)."***".substr($email,$at);
	}

	//長文字截斷
	function cutstr($str,$len){
		if(mb_strlen($str)>$len){
			return mb_substr($str,0,$len)."…";
		}
		return $str;
	}

	//星等 1~5
	function stars($rating){
		$html="";
		for($i=1;$i<=5;$i=$i+1){
			$html=$html."<span class=\"".($i<=$rating?"star on":"star")."\">★</span>";
		}
		return "<span class=\"stars\" title=\"評分 ".$rating." 顆星（滿分 5）\">".$html."</span>";
	}

	//推薦程度色塊
	function recommendtag($recommendation){
		if($recommendation=="高"){
			$class="tag high";
		}elseif($recommendation=="中"){
			$class="tag mid";
		}elseif($recommendation=="低"){
			$class="tag low";
		}else{
			$class="tag none";
			$recommendation="無資料";
		}
		return "<span class=\"".$class."\">".$recommendation."</span>";
	}

	//照片欄位為空時改用佔位圖
	function photo($path){
		if($path==""||$path==null){
			return "media/images/placeholder-600x400.png";
		}
		return $path;
	}
?>
