<?php

$this->require_admin ();

$page->layout = 'admin';

$cur = $this->installed ('products', $appconf['Admin']['version']);

if ($cur === true) {
	$page->title = 'Already installed';
	echo '<p><a href="/products/admin">Continue</a></p>';
	return;
} elseif ($cur !== false) {
	header ('Location: /' . $appconf['Admin']['upgrade']);
	exit;
}

$page->title = 'Installing App: Products';

$conn = conf ('Database', 'master');
$driver = $conn['driver'];
DB::beginTransaction ();

$error = false;
$sqldata = sql_split (file_get_contents ('apps/products/conf/install_' . $driver . '.sql'));
foreach ($sqldata as $sql) {
	if (! DB::execute ($sql)) {
		$error = DB::error ();
		break;
	}
}

if ($error) {
	DB::rollback ();
	echo '<p class="visible-notice">Error: ' . $error . '</p>';
	echo '<p>Install failed.</p>';
	return;
}
DB::commit ();

echo '<p><a href="/products/admin">Done.</a></p>';

$this->mark_installed ('products', $appconf['Admin']['version']);

?>