<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Add Product');

$form = new Form ('post', $this);

$form->data = array (
	'_categories' => products\Category::query ()->order ('name', 'asc')->fetch_field ('name'),
	'_taxes' => products\Tax::query ()->order ('name', 'asc')->fetch_field ('name'),
	'quantity' => '-1',
	'taxes' => array ()
);

echo $form->handle (function ($form) {
	// Create and save a new product 
	$product = new products\Product (array (
		'id' => $_POST['id'], 
		'name' => $_POST['name'], 
		'price' => $_POST['price'] * 100, 
		'photo' => $_POST['photo'], 
		'category' => $_POST['category'], 
		'description' => $_POST['description'], 
		'download' => $_POST['download'], 
		'quantity' => $_POST['quantity'], 
		'taxes' => is_array ($_POST['taxes']) ? json_encode ($_POST['taxes']) : '[]',
		'address' => $_POST['address'],
		'details' => $_POST['details'],
		'shipping' => $_POST['shipping'] * 100
	));
	$product->put ();

	if ($product->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save product.'));
		return false;
	}

	// Save a version of the product 
	Versions::add ($product);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Product added.'));
	$form->controller->redirect ('/products/admin');
});

?>