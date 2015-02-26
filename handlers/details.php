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

$p = $product->orig ();
$p->total = $p->price;
$taxes = products\Tax::taxes (json_decode ($p->taxes));
foreach ($taxes as $tax => $percent) {
	$p->total += round (products\Tax::calculate ($p->price, $percent));
}
$p->payment_description = $p->name . ' (#' . str_pad ($p->id, 3, '0', STR_PAD_LEFT) . ')';
$p->address = $p->address ? 'Yes' : 'No';
$p->details = $tpl->run_includes ($p->details);

echo $tpl->render ('products/details', $p);
