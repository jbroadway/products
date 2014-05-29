<?php

namespace products;

/**
 * Fields:
 *
 * - name
 * - percent
 */
class Tax extends \Model {
	public $table = '#prefix#products_taxes';
	public $key = 'name';

	/**
	 * Takes a list of taxes as an array and returns
	 * the list of taxes and their percents.
	 *
	 *     $taxes = products\Taxes::taxes (array ('GST', 'PST'));
	 *     // array ('GST' => 5, 'PST' => 8)
	 *
	 * Note: Uses the unescaped `$list` in a database query,
	 * so be sure to never pass this method user input data.
	 */
	public static function taxes ($list) {
		if (count ($list) === 0) {
			return array ();
		}

		return self::query ()
			->where ('name in("' . join ('","', $list) . '")')
			->fetch_assoc ('name', 'percent');
	}

	/**
	 * Calculate the tax on an amount and percent.
	 *
	 *     $amount = products\Tax::calculate (1000, 5); // 50
	 */
	public static function calculate ($amount, $percent) {
		return $amount * ($percent / 100);
	}
}

?>