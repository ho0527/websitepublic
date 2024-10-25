<?php
	function partition($limit, $now, $pointer, &$parts) {
		if ($now < 0)
			return;
		if (!$now) {
			for ($i = 0; $i < $pointer; $i++)
				echo $parts[$i]." ";
			echo "\n";
			return;
		}
		for ($i = $limit; $i >= 1; $i--) {
			$parts[$pointer] = $i;
			partition($i, $now - $i, $pointer + 1, $parts);
		}
	}

	function main() {
		$parts = array_fill(0, 101, 0);
		$number = readline("Enter a number: ");
		while ($number) {
			partition($number, $number, 0, $parts);
			$number = readline("Enter a number: ");
		}
	}

	main()
?>