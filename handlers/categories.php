<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Product Categories');

// Fetch the items and total items
$items = products\Category::query ()->order ('name', 'asc')->fetch ();

// Pass our data to the view template
echo $tpl->render (
	'products/categories',
	array (
		'items' => $items,
		'count' => count ($items)
	)
);

?>