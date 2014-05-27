<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Products');

// Calculate the offset
$limit = 20;
$num = isset ($this->params[0]) ? $this->params[0] : 1;
$offset = ($num - 1) * $limit;

// Fetch the items and total items
$items = products\Product::query ()->fetch ($limit, $offset);
$total = products\Product::query ()->count ();

// Check for error, e.g., if table hasn't been created yet
if ($items === false) {
	$items = array ();
	$total = 0;
	printf (
		'<p class="visible-notice"><strong>%s</strong>: %s</p>',
		__ ('Notice'),
		__ ('It looks like you need to import your database schema for this app.')
	);
}

// Pass our data to the view template
echo $tpl->render (
	'products/admin',
	array (
		'limit' => $limit,
		'total' => $total,
		'items' => $items,
		'count' => count ($items),
		'url' => '/products/admin/%d'
	)
);

?>