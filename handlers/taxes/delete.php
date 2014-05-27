<?php

$this->require_admin ();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/products/taxes');
}

$tax = new products\Tax;
$tax->remove ($_POST['id']);

if ($tax->error) {
	$this->add_notification (__ ('Unable to delete tax.'));
	$this->redirect ('/products/taxes');
}

$this->add_notification (__ ('Tax deleted.'));
$this->redirect ('/products/taxes');

?>