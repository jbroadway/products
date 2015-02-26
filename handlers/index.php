<?php

$page->id = 'products';
$page->title = Appconf::products ('Products', 'title');
$page->layout = Appconf::products ('Products', 'layout');
$this->run ('admin/util/minimal-grid');
$page->add_style ('/apps/products/css/products.css');

$products = products\Product::query ()
	->order ('category', 'asc')
	->order ('name', 'asc')
	->fetch_orig ();

echo $tpl->render (
	'products/index',
	array (
		'products' => $products,
		'category' => null
	)
);

?>
