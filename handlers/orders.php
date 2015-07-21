<?php

/**
 * Shows the list of orders to the admin.
 */

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Orders');

// clear pending orders over 72 hours old
products\Order::clear_pending ();

$limit = 20;
$num = isset ($_GET['offset']) ? $_GET['offset'] : 1;
$offset = ($num - 1) * $limit;

$q = isset ($_GET['q']) ? $_GET['q'] : '';
$q2 = str_replace (array ('user:', 'status:'), array ('o.user_id:', 'o.status:'), $q);

$q_query = 'o.*, u.name';
$q_from = '#prefix#products_order o, #prefix#user u';
$q_fields = array ('o.id', 'o.ts', 'o.status', 'u.name', 'u.email');
$q_exact = array ('o.user_id', 'o.status');
$url = ! empty ($q)
	? '/products/orders?q=' . urlencode ($q) . '&offset=%d'
	: '/products/orders?offset=%d';

$list = products\Order::query ($q_query)
	->from ($q_from)
	->where_search ($q2, $q_fields, $q_exact)
	->and_where ('o.user_id = u.id')
	->order ('ts', 'desc')
	->fetch_orig ($limit, $offset);

$count =products\Order::query ($q_query)
	->from ($q_from)
	->where_search ($q2, $q_fields, $q_exact)
	->and_where ('o.user_id = u.id')
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
