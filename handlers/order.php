<?php

if (count ($this->params) < 2) $this->redirect ('/products');
if (! isset ($this->params[2])) $this->params[2] = null;

$page->layout = Appconf::products ('Products', 'layout');

$order = new stripe\Payment ($this->params[0]);

if ($order->error) {
	error_log ('Order not found (' . $this->params[0] . '): ' . $product->error);
	echo $this->error (
		404,
		__ ('Order not found'),
		__ ('Order #%d was not found.', $this->params[0])
	);
	return;
}

$product = new products\Product ($this->params[1]);

if ($product->error) {
	error_log ('Product not found (' . $this->params[1] . '): ' . $product->error);
	echo $this->error (
		404,
		__ ('Product not found'),
		__ ('Product #%d was not found.', $this->params[1])
	);
	return;
}

if (! preg_match ('/\(\#' . str_pad ($product->id, 3, '0', STR_PAD_LEFT) . '\)/', $order->description)) {
	error_log ('Order and product do not match');
	echo $this->error (
		404,
		__ ('Invalid order'),
		__ ('The order info could not be verified.')
	);
	return;
}


if ($this->params[2] === 'completed') {
	$page->title = __ ('Order confirmed');

	try {
		Mailer::send (array (
			
		));
	} catch (Exception $e) {
	}
} elseif ($this->params[2] === 'download') {
	$page->layout = false;
	$this->header ('Cache-control: private');
	$this->header ('Content-disposition: attachment; filename="' . basename ($product->download) . '"');
	if (Appconf::products ('Products', 'xsendfile') === 'lighttpd') {
		$this->header ('X-Sendfile: ' . getcwd () . $product->download);
		exit;
	} elseif (Appconf::products ('Products', 'xsendfile') === 'nginx') {
		$this->header ('X-Accel-Redirect: ' . getcwd () . $product->download);
		exit;
	}
	$this->header ('Content-length: ' . filesize (ltrim ($product->download, '/')));
	readfile (ltrim ($product->download, '/'));
	exit;
} else {
	$page->title = __ ('Order') . ' ' . str_pad ($order->id, 3, '0', STR_PAD_LEFT) . '-' . $product->id;
}

echo $tpl->render (
	'products/order',
	array (
		'product' => $product->orig (),
		'order' => $order->orig (),
		'action' => $this->params[2],
		'taxes' => products\Tax::taxes (json_decode ($product->taxes))
	)
);

?>