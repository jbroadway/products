<?php

/**
 * Download files from completed orders.
 *
 * Parameters:
 *
 * - products\Order ID
 * - products\Product ID
 */

if (count ($this->params) < 2) $this->redirect ('/products');

// Ensure user is logged in
$this->require_login ();

$order = new products\Order ($this->params[0]);

if ($order->error) {
	error_log ('Order not found (' . $this->params[0] . '): ' . $order->error);
	echo $this->error (
		404,
		__ ('Order not found'),
		__ ('Order #%d was not found.', $this->params[0])
	);
	return;
}

$user_id = User::val ('id');
if (! User::require_admin () && $order->user_id != $user_id) {
	error_log ('Order belongs to other user (order: ' . $this->params[0] . ', user: ' . $user_id . ')');
	echo $this->error (
		404,
		__ ('Order not found'),
		__ ('Order #%d was not found.', $this->params[0])
	);
	return;
}

$items = json_decode ($order->items);

foreach ($items as $item) {
	if ($item->id == $this->params[1]) {
		$product = new products\Product ($item->id);
		if ($product->error) {
			error_log ('Product not found (order: ' . $this->params[0] . ', product: ' . $item->id . ')');
			echo $this->error (
				404,
				__ ('Product not found'),
				__ ('The product was not found. Please contact us for more information.')
			);
			return;
		}

		$page->layout = false;
		while (ob_get_level () > 0) {
			ob_end_clean ();
		}

		$this->header ('Cache-control: private');
		$this->header ('Pragma: no-cache');
		$this->header ('Content-type: application/octet-stream');
		$this->header ('Content-disposition: attachment; filename="' . basename ($product->download) . '"');

		if (Appconf::products ('Products', 'xsendfile') === 'lighttpd') {
			$this->header ('X-Sendfile: ' . getcwd () . $product->download);
			exit;

		} elseif (Appconf::products ('Products', 'xsendfile') === 'nginx') {
			$this->header ('X-Accel-Redirect: ' . getcwd () . $product->download);
			exit;
		}

		$this->header ('Content-length: ' . filesize (getcwd () . $product->download));
		readfile (getcwd () . $product->download);
		exit;
	}
}

echo $this->error (
	404,
	__ ('Download not available'),
	__ ('The product was not found. Please contact us for more information.')
);
