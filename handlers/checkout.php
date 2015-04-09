<?php

$this->run ('admin/util/minimal-grid');

$page->id = 'products';
$page->title = __ ('Checkout');
$page->add_style ('/apps/products/css/products.css');
$page->add_script ('/js/json2.js');
$page->add_script ('/js/jstorage.js');
$page->add_script ('/apps/products/js/handlebars-v3.0.1.js');
$page->add_script ('/apps/products/js/accounting.min.js');
$page->add_script ('/apps/products/js/cart.js');

echo $tpl->render ('products/checkout', array (
	'taxes' => products\Tax::query ()->fetch_assoc ('name', 'percent'),
	'max_shipping' => Appconf::products ('Products', 'max_shipping'),
	'shipping_free_over' => Appconf::products ('Products', 'shipping_free_over')
));
