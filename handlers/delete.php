<?php

$this->require_admin ();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/products/admin');
}

$product = new products\Product;
$product->remove ($_POST['id']);

if ($product->error) {
	$this->add_notification (__ ('Unable to delete product.'));
	$this->redirect ('/products/admin');
}

$this->add_notification (__ ('Product deleted.'));
$this->redirect ('/products/admin');

?>