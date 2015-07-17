<?php

/**
 * Accepts completed orders.
 *
 * Parameters:
 *
 * - stripe\Payment ID
 * - products\Order ID
 * - status (completed|download)
 */

if (count ($this->params) < 2) $this->redirect ('/products');
if (! isset ($this->params[2])) $this->params[2] = null;

$page->id = 'products';
$page->title = __ ('Order') . ' #' . Template::sanitize ($this->params[1]);
$page->layout = Appconf::products ('Products', 'layout');

// Ensure user is logged in
$this->require_login ();

$pmt = new stripe\Payment ($this->params[0]);

if ($pmt->error) {
	error_log ('Payment not found (' . $this->params[0] . '): ' . $pmt->error);
	echo $this->error (
		404,
		__ ('Payment not found'),
		__ ('Payment #%d was not found.', $this->params[0])
	);
	return;
}

$order = new products\Order ($this->params[1]);

if ($order->error) {
	error_log ('Order not found (' . $this->params[1] . '): ' . $order->error);
	echo $this->error (
		404,
		__ ('Order not found'),
		__ ('Order #%d was not found.', $this->params[1])
	);
	return;
}

if (! preg_match ('/ \#' . $order->id . '$/', $pmt->description)) {
	error_log ('Order and payment do not match');
	echo $this->error (
		404,
		__ ('Invalid order'),
		__ ('The order info could not be verified.')
	);
	return;
}

$user_id = User::val ('id');
if ($pmt->user_id != $user_id || $order->user_id != $user_id) {
	error_log ('Order belongs to other user (order: ' . $this->params[1] . ', user: ' . $user_id . ')');
	echo $this->error (
		404,
		__ ('Order not found'),
		__ ('Order #%d was not found.', $this->params[1])
	);
	return;
}

// Get taxes, items, and item IDs
$taxes = json_decode ($order->taxes);
$items = json_decode ($order->items);
$ids = array_map (function ($o) { return $o->id; }, $items);
$qs = array_map (function ($o) { return '?'; }, $items);

// Mark order completed
$send_receipt = false;
if ($order->status === 'pending') {
	$order->payment_id = $pmt->id;
	$order->status = 'completed';
	$order->put ();
	$send_receipt = true;
	unset ($_SESSION['products_order']);

	// Reduce inventory
	DB::execute (
		'update #prefix#products
		 set quantity = (quantity - 1)
		 where id in(' . join (',', $qs) . ')
		 and quantity > 0',
		 $ids
	);
}

$page->add_style ('/apps/products/css/products.css');

if ($this->params[2] === 'completed' && $send_receipt) {
	$page->title .= ' ' . __ ('Confirmed');
	$page->add_script ('/js/json2.js');
	$page->add_script ('/js/jstorage.js');
	$page->add_script ('/apps/products/js/handlebars-v3.0.1.js');
	$page->add_script ('/apps/products/js/accounting.min.js');
	$page->add_script ('/apps/products/js/cart.js');

	// send email receipt
	try {
		Mailer::send (array (
			'to' => array ($order->email),
			'subject' => 'Receipt for order #' . $order->id,
			'text' => $tpl->render (
				'products/email/receipt',
				array (
					'payment' => $pmt->orig (),
					'order' => $order->orig (),
					'taxes' => $taxes,
					'items' => $items
				)
			)
		));
	} catch (Exception $e) {
	}

	// notify admin of sale
	$notify = Appconf::products ('Products', 'notify');
	if ($notify && ! empty ($notify)) {
		try {
			Mailer::send (array (
				'to' => array ($notify),
				'subject' => 'New order received #' . $order->id,
				'text' => $tpl->render (
					'products/email/notify',
					array (
						'payment' => $pmt->orig (),
						'order' => $order->orig (),
						'taxes' => $taxes,
						'items' => $items
					)
				)
			));
		} catch (Exception $e) {
		}
	}
}

// Fetch downloads to add links
$downloads = products\Product::query ('id, name, download')
	->where (function ($q) use ($ids) {
		foreach ($ids as $n => $id) {
			$tmp = ($n === 0)
				? $q->where ('id', $id)
				: $q->or_where ('id', $id);
		}
	})
	->where ('download != ""')
	->fetch_orig ();

echo $tpl->render (
	'products/order',
	array (
		'order' => $order->orig (),
		'payment' => $pmt->orig (),
		'action' => $this->params[2],
		'taxes' => $taxes,
		'items' => $items,
		'clear_cart' => $send_receipt,
		'downloads' => $downloads
	)
);
