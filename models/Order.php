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
class Order extends \ExtendedModel {
	public $table = '#prefix#products_order';
	
	public $_extended_field = 'items';
}
