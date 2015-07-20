<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Product Orders');

$user = isset ($_GET['user']) ? $_GET['user'] : false;
$status = isset ($_GET['status']) ? $_GET['status'] : false;

$limit = 20;
$num = isset ($_GET['offset']) ? $_GET['offset'] : 1;
$offset = ($num - 1) * $limit;

$q = products\Order::query ();
if ($user) {
	$q->where ('user_id', $user);
}
if ($status) {
	$q->where ('status', $status);
}

$q->order ('ts', 'desc');

$list = $q->fetch_orig ($limit, $offset);
$count = $q->count ();

$users = User::query ('id, name')
	->order ('name', 'asc')
	->fetch_assoc ('id', 'name');

$statuses = array (
	'pending' => __ ('Pending'),
	'completed' => __ ('Completed'),
	'shipping' => __ ('Shipping'),
	'shipped' => __ ('Shipped')
);

echo $tpl->render (
	'products/orders',
	array (
		'limit' => $limit,
		'total' => $count,
		'list' => $list,
		'count' => count ($list),
		'user' => $user,
		'users' => $users,
		'status' => $status,
		'statuses' => $statuses,
		'url' => '/products/orders?user=' . Template::sanitize ($user) . '&status=' . Template::sanitize ($status) . '&offset=%d'
	)
);
