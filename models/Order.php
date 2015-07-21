<?php

namespace products;

/**
 * Order information and contents.
 *
 * Properties:
 *
 * - id				Order ID
 * - user_id		User ID
 * - payment_id		Payment ID
 * - ts				Date/time
 * - status			Order status (pending,completed,shipped)
 * - subtotal		Order subtotal before taxes and shipping
 * - shipping		Shipping total
 * - taxes			JSON-encoded array of taxes, calculated on subtotal and shipping
 * - total			Order total including taxes and shipping
 * - items			JSON-encoded array of ordered items (id, label, price, qty, taxes, shipping)
 */
class Order extends \Model {
	public $table = '#prefix#products_order';
	
	//public $_extended_field = 'items';

	/**
	 * Returns a list of valid statuses as key/value pairs, or the label
	 * value for a given status if used as a template filter, e.g.:
	 *
	 *     {{status|products\Order:statuses}}
	 */
	public static function statuses ($status = null) {
		$statuses = array (
			'pending' => __ ('Payment Pending'),
			'completed' => __ ('Payment Completed'),
			'partial' => __ ('Order Partially Shipped'),
			'shipped' => __ ('Order Shipped'),
			'cancelled' => __ ('Order Cancelled'),
			'refunded' => __ ('Order Refunded')
		);

		if ($status !== null) {
			return $statuses[$status];
		}

		return $statuses;
	}
	
	/**
	 * Is the given status valid? For input validation.
	 */
	public static function status_valid ($status) {
		$statuses = self::statuses ();
		return isset ($statuses[$status]);
	}
}
