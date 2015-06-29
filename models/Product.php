<?php

namespace products;

/**
 * Fields:
 *
 * - id
 * - name
 * - price
 * - photo
 * - category
 * - description
 * - download
 * - quantity
 * - taxes
 * - details
 * - shipping
 */
class Product extends \Model {
	public $table = '#prefix#products';
	public $key = 'id';
	
	/**
	 * Get the product's discount price based on the specified discount
	 * value, which is an integer.
	 */
	public function discount_price ($discount = 0) {
		if ($discount > 0) {
			return $this->price - ($this->price * ($discount / 100));
		}
		return $this->price;
	}
}

?>