<?php

$this->run ('admin/util/minimal-grid');

$page->id = 'products';
$page->title = __ ('Shopping Cart');
$page->add_style ('/apps/products/css/products.css');
$page->add_script ('/js/json2.js');
$page->add_script ('/js/jstorage.js');
$page->add_script ('/apps/products/js/accounting.min.js');
$page->add_script ('/apps/products/js/cart.js');

echo $tpl->render ('products/cart');
