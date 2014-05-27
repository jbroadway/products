<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Product Taxes');

// Fetch the items and total items
$items = products\Tax::query ()->order ('name', 'asc')->fetch ();

// Pass our data to the view template
echo $tpl->render (
	'products/taxes',
	array (
		'items' => $items,
		'count' => count ($items)
	)
);

?>