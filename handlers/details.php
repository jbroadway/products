<?php

// Verify that they're on an SSL connection
//$this->force_https ();

if (! count ($this->params)) {
	$this->redirect ('/products');
}

$id = $this->params[0];

$product = new products\Product ($id);
if ($product->error) {
	$this->redirect ('/products');
}

$this->run ('admin/util/minimal-grid');

$page->id = 'products';
$page->title = $product->name;
$page->add_style ('/apps/products/css/products.css');
$page->add_script ('/js/json2.js');
$page->add_script ('/js/jstorage.js');
$page->add_script ('/apps/products/js/handlebars-v3.0.1.js');
$page->add_script ('/apps/products/js/accounting.min.js');
$page->add_script ('/apps/products/js/cart.js');

$discount = products\App::discount ();
$allow_invoice = products\App::allow_invoice ();

$p = $product->orig ();
$p->discount = $discount;
$p->discount_price = $product->discount_price ($discount);
$p->total = $p->discount_price;
$taxes = products\Tax::taxes (json_decode ($p->taxes));
foreach ($taxes as $tax => $percent) {
	$p->total += round (products\Tax::calculate ($p->discount_price, $percent));
}
$p->payment_description = $p->name . ' (#' . str_pad ($p->id, 3, '0', STR_PAD_LEFT) . ')';
$p->address = $p->address ? 'Yes' : 'No';
$p->details = $tpl->run_includes ($p->details);

echo $tpl->render ('products/details', $p);
