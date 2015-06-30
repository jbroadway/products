<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Product Orders');

echo $tpl->render (
	'products/orders',
	array (
	)
);
