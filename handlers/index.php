<?php

$page->title = Appconf::products ('Products', 'title');
$page->layout = Appconf::products ('Products', 'layout');

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