<?php

/**
 * Shows the list of orders to the admin.
 */

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Orders');

$limit = 20;
$num = isset ($_GET['offset']) ? $_GET['offset'] : 1;
$offset = ($num - 1) * $limit;
$q = isset ($_GET['q']) ? $_GET['q'] : '';
$q_fields = array ('id', 'ts', 'status');
$q_exact = array ('user_id', 'status');
$url = ! empty ($q)
	? '/products/orders?q=' . urlencode ($q) . '&offset=%d'
	: '/products/orders?offset=%d';

$list = products\Order::query ()
	->where_search ($q, $q_fields, $q_exact)
	->order ('ts', 'desc')
	->fetch_orig ($limit, $offset);

$count =products\Order::query ()
	->where_search ($q, $q_fields, $q_exact)
	->count ();

$users = User::query ('id, name')
	->order ('name', 'asc')
	->fetch_assoc ('id', 'name');

$statuses = products\Order::statuses ();

echo $tpl->render (
	'products/orders',
	array (
		'limit' => $limit,
		'total' => $count,
		'list' => $list,
		'count' => count ($list),
		'users' => $users,
		'statuses' => $statuses,
		'url' => $url,
		'q' => $q
	)
);
