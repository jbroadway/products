<?php

$page->id = 'products';
$page->title = Appconf::products ('Products', 'title');
$page->layout = Appconf::products ('Products', 'layout');
$this->run ('admin/util/minimal-grid');
$page->add_style ('/apps/products/css/products.css');

$discount = products\App::discount ();
$allow_invoice = products\App::allow_invoice ();

$products = products\Product::query ()
	->order ('category', 'asc')
	->order ('name', 'asc')
	->fetch ();

foreach ($products as $k => $p) {
	$p->discount_price = $p->discount_price ($discount);
	$products[$k] = $p->orig ();
}

echo $tpl->render (
	'products/index',
	array (
		'products' => $products,
		'category' => null,
		'discount' => $discount,
		'allow_invoice' => $allow_invoice
	)
);
