<?php

$this->require_admin ();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/products/categories');
}

$cat = new products\Category;
$cat->remove ($_POST['id']);

if ($cat->error) {
	$this->add_notification (__ ('Unable to delete category.'));
	$this->redirect ('/products/categories');
}

$this->add_notification (__ ('Category deleted.'));
$this->redirect ('/products/categories');

?>