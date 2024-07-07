<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Number of Days</title>
	</head>
	<body>
		<h4>Calculate number of days</h4>
		<form>
			<label for="date1">Date 1:
				<input type="date" name="date1">
			</label>

			<label for="date2">Date 2:
				<input type="date" name="date2">
			</label>

			<input type="submit" name="submit" value="送出">
		</form>
		<?php

			function numbertoenword($number){
				$words=array(
					0 => "zero",
					1 => "one",
					2 => "two",
					3 => "three",
					4 => "four",
					5 => "five",
					6 => "six",
					7 => "seven",
					8 => "eight",
					9 => "nine",
					10 => "ten",
					11 => "eleven",
					12 => "twelve",
					13 => "thirteen",
					14 => "fourteen",
					15 => "fifteen",
					16 => "sixteen",
					17 => "seventeen",
					18 => "eighteen",
					19 => "nineteen",
					20 => "twenty",
					30 => "thirty",
					40 => "forty",
					50 => "fifty",
					60 => "sixty",
					70 => "seventy",
					80 => "eighty",
					90 => "ninety"
				);

				if(0<=$number&&$number<=20){
					return $words[$number];
				}elseif(21<=$number&&$number<=99){
					$tens=floor($number/10)*10;
					$units=$number%10;
					return $words[$tens]."-".$words[$units];
				}elseif($number >= 100 && $number <= 999){
					$hundreds=floor($number/100);
					$remainder=$number%100;

					$result=$words[$hundreds]." hundred";

					if($remainder>0){
						$result=$result." and ".numbertoenword($remainder);
					}

					return $result;
				}elseif(1000<=$number&&$number<=999999){
					$thousands=floor($number/1000);
					$remainder=$number%1000;

					$result=numbertoenword($thousands)." thousand";

					if($remainder>0){
						$result=$result." ".numbertoenword($remainder);
					}

					return $result;
				}else{
					return "Number out of range";
				}
			}

			if(isset($_GET["submit"])){
				$date1=$_GET["date1"];
				$date2=$_GET["date2"];
				$diff=abs(strtotime($date2)-strtotime($date1));
				$days=floor($diff/(60*60*24));
				?>
				<p>Output: <?php echo(numbertoenword($days)." days") ?></p>
				<?php
			}
		?>
	</body>
</html>