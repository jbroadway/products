<?php

namespace products;

/**
 * Fields:
 *
 * - name
 */
class Category extends \Model {
	public $table = '#prefix#products_category';
	public $key = 'name';
}

?>