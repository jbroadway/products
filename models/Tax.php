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
}

?>