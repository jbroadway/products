<?php

namespace products;

class Filter {
	public static function money ($amt) {
		$amt = $amt / 100;
		return number_format ($amt, 2);
	}
}

?>