<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Add Category');

$form = new Form ('post', $this);

echo $form->handle (function ($form) {
	// Create and save a new product 
	$cat = new products\Category (array (
		'name' => $_POST['name']
	));
	$cat->put ();

	if ($cat->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save category.'));
		return false;
	}

	// Save a version of the product 
	Versions::add ($cat);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Category added.'));
	$form->controller->redirect ('/products/categories');
});

?>