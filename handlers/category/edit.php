<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Edit Category');

$form = new Form ('post', $this);

$form->data = new products\Category ($_GET['id']);

echo $form->handle (function ($form) {
	// Update the product 
	$cat = $form->data;
	$cat->name = $_POST['name'];
	$cat->put ();

	if ($cat->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save category.'));
		return false;
	}

	// Save a version of the category 
	Versions::add ($cat);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Category saved.'));
	$form->controller->redirect ('/products/categories');
});

?>