<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Add Tax');

$form = new Form ('post', $this);

echo $form->handle (function ($form) {
	// Create and save a new tax 
	$tax = new products\Tax (array (
		'name' => $_POST['name'],
		'percent' => $_POST['percent'],
		'charge_on_shipping' => $_POST['charge_on_shipping']
	));
	$tax->put ();

	if ($tax->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save tax.'));
		return false;
	}

	// Save a version of the tax 
	Versions::add ($tax);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Tax added.'));
	$form->controller->redirect ('/products/taxes');
});

?>