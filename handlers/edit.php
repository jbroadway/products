<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Edit Product');

$form = new Form ('post', $this);

$product = new products\Product ($_GET['id']);
$form->data = $product->orig ();
$form->data->price = $form->data->price / 100;
$form->data->taxes = json_decode ($form->data->taxes);
$form->data->_categories = products\Category::query ()->order ('name', 'asc')->fetch_field ('name');
$form->data->_taxes = products\Tax::query ()->order ('name', 'asc')->fetch_field ('name');

echo $form->handle (function ($form) use ($product) {
	// Update the product 
	$product->name = $_POST['name'];
	$product->price = $_POST['price'] * 100;
	$product->photo = $_POST['photo'];
	$product->category = $_POST['category'];
	$product->description = $_POST['description'];
	$product->download = $_POST['download'];
	$product->quantity = $_POST['quantity'];
	$product->taxes = is_array ($_POST['taxes']) ? json_encode ($_POST['taxes']) : '[]';
	$product->put ();

	if ($product->error) {
		// Failed to save
		$form->controller->add_notification (__ ('Unable to save product.') . $product->error);
		return false;
	}

	// Save a version of the product 
	Versions::add ($product);

	// Notify the user and redirect on success
	$form->controller->add_notification (__ ('Product saved.'));
	$form->controller->redirect ('/products/admin');
});

?>