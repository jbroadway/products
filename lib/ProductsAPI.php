<?php

namespace products;

/**
 * REST API for the Products app.
 */
class ProductsAPI extends \Restful {
	/**
	 * Fetch details for the specified list of products.
	 *
	 *     GET /products/api/checkout_info?items[]=1&items[]=2
	 *
	 * Parameters:
	 *
	 * - items		An array of item IDs
	 *
	 * Returns the following fields about each product:
	 *
	 * - id			Product ID
	 * - price		Price
	 * - quantity	Quantity available
	 * - taxes		Taxes to charge
	 * - shipping	Shipping per item
	 */
	public function get_checkout_info () {
		if (! isset ($_GET['items'])) {
			return $this->error ('Missing parameter: items');
		}
		
		if (! is_array ($_GET['items'])) {
			return $this->error ('Invalid parameter: items');
		}

		$ids = $_GET['items'];
		
		$res = Product::query ('id, name, price, quantity, taxes, shipping')
			->where (function ($q) use ($ids) {
				foreach ($ids as $k => $id) {
					if ($k === 0) {
						$q->where ('id', $id);
					} else {
						$q->or_where ('id', $id);
					}
				}
			})
			->fetch_orig ();

		foreach ($res as $k => $p) {
			$res[$k]->taxes = json_decode ($p->taxes);
		}

		return $res;
	}
}
