<?php

$this->run ('admin/util/minimal-grid');

$page->id = 'products';
$page->title = __ ('Checkout');
$page->layout = Appconf::products ('Products', 'layout');

// Ensure user is logged in
if (! User::require_login ()) {
	echo $tpl->render ('products/login');
	return;
}

// Create a new order and redirect to /products/checkout/ORDER_ID
if (! isset ($this->params[0])) {
	if (isset ($_SESSION['products_order'])) {
		$this->redirect ('/products/checkout/' . $_SESSION['products_order']);
	}

	$o = new products\Order (array (
		'user_id' => User::val ('id'),
		'payment_id' => 0,
		'ts' => gmdate ('Y-m-d H:i:s'),
		'status' => 'pending',
		'subtotal' => 0,
		'shipping' => 0,
		'taxes' => '[]',
		'total' => 0,
		'items' => '[]'
	));

	if (! $o->put ()) {
		printf ('<p>%s</p>', __ ('An unknown error occurred. Please try again later.'));
		return;
	}
	
	$_SESSION['products_order'] = $o->id;
	$this->redirect ('/products/checkout/' . $o->id);
}

// Fetch the order
$o = new products\Order ($this->params[0]);
if ($o->error) {
	printf ('<p>%s</p>', __ ('An unknown error occurred. Please try again later.'));
	return;
}

$page->add_style ('/apps/products/css/products.css');
$page->add_script ('/js/json2.js');
$page->add_script ('/js/jstorage.js');
$page->add_script ('/apps/products/js/handlebars-v3.0.1.js');
$page->add_script ('/apps/products/js/accounting.min.js');
$page->add_script ('/apps/products/js/cart.js');

// Create a POST request to save the order info from the cart
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo $tpl->render ('products/checkout_post', array (
		'taxes' => products\Tax::query ()->fetch_orig (),
		'max_shipping' => Appconf::products ('Products', 'max_shipping'),
		'shipping_free_over' => Appconf::products ('Products', 'shipping_free_over'),
		'order' => $o->orig ()
	));
	return;
}

// TODO: validation

// Update the order info from the POST request
$o->subtotal = $_POST['subtotal'];
$o->shipping = $_POST['shipping'];
$o->taxes = $_POST['taxes'];
$o->total = $_POST['total'];
$o->items = $_POST['items'];
$o->put ();

// Show order summary and payment button
echo $tpl->render ('products/checkout', array (
	'taxes' => products\Tax::query ()->fetch_orig (),
	'max_shipping' => Appconf::products ('Products', 'max_shipping'),
	'shipping_free_over' => Appconf::products ('Products', 'shipping_free_over'),
	'order' => $o,
	'payment_description' => 'Order #' . $o->id,
	'total' => $o->total
));
