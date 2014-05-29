<?php

$page->title = Appconf::products ('Products', 'title');
$page->layout = Appconf::products ('Products', 'layout');

$products = products\Product::query ()
	->order ('category', 'asc')
	->order ('name', 'asc')
	->fetch_orig ();

foreach ($products as $k => $p) {
	$products[$k]->total = $p->price;
	$taxes = products\Tax::taxes (json_decode ($p->taxes));
	foreach ($taxes as $tax => $percent) {
		$products[$k]->total += products\Tax::calculate ($p->price, $percent);
	}
	$products[$k]->payment_description = $p->name . ' (#' . str_pad ($p->id, 3, '0', STR_PAD_LEFT) . ')';
}

echo $tpl->render (
	'products/index',
	array (
		'products' => $products,
		'category' => null
	)
);

?>