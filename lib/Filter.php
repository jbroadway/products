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
	
	/**
	 * Pad a string to the specified length with spaces, left or right
	 * aligned. Used in the order receipt emails.
	 */
	public static function padding ($text, $length = 10, $align = 'left') {
		if (strlen ($text) > $length) {
			return substr ($text, 0, $length - 3) . '...';
		}

		switch ($align) {
			case 'right':
				return str_pad ($text, $length, ' ', STR_PAD_LEFT);
			case 'left':
			default:
				return str_pad ($text, $length, ' ', STR_PAD_RIGHT);
		}
	}
}

?>