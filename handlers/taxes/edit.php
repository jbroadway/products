<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Edit Tax');

$form = new Form ('post', $this);

$form->data = new products\Tax ($_GET['id']);

echo $form->handle (function ($form) {
	// Update the tax 
	$tax = $form->data;
	$tax->name = $_POST['name'];
	$tax->percent = $_POST['percent'];
	$tax->put ();

	if ($tax->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save tax.'));
		return false;
	}

	// Save a version of the tax 
	Versions::add ($tax);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Tax saved.'));
	$form->controller->redirect ('/products/taxes');
});

?>