<?php

namespace products;

class Filter {
	public static function money ($amt) {
		$amt = $amt / 100;
		return number_format ($amt, 2);
	}
	
	public static function taxes ($taxes) {
		$taxes = json_decode ($taxes);
		return join (', ', $taxes);
	}
	
	public static function quantity ($q) {
		if ($q == -1) {
			return __ ('Unlimited');
		}
		return $q;
	}
}

?>