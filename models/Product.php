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
 */
class Product extends \Model {
	public $table = '#prefix#products';
	public $key = 'id';
}

?>