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
}

?>