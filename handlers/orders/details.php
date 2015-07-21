<?php

/**
 * Shows an order's details to the admin.
 *
 * Parameters:
 *
 * - products\Order ID
 */

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Order') . ' #' . Template::sanitize ($_GET['id']);

$order = new products\Order ($_GET['id']);
$pmt = new stripe\Payment ($order->payment_id);
$user = new User ($order->user_id);
$taxes = json_decode ($order->taxes);
$items = json_decode ($order->items);
$ids = array_map (function ($o) { return $o->id; }, $items);

// Fetch downloads to add links
$downloads = products\Product::query ('id, name, download')
	->where (function ($q) use ($ids) {
		foreach ($ids as $n => $id) {
			$tmp = ($n === 0)
				? $q->where ('id', $id)
				: $q->or_where ('id', $id);
		}
	})
	->where ('download != ""')
	->fetch_orig ();

$form = new Form ('post', $this);

$form->data = array (
	'user' => $user->orig (),
	'order' => $order->orig (),
	'payment' => $pmt->orig (),
	'taxes' => $taxes,
	'items' => $items,
	'downloads' => $downloads,
	'statuses' => products\Order::statuses ()
);

echo $form->handle (function ($form) use ($order, $user, $tpl) {
	if ($_POST['status'] === $order->status) {
		$form->controller->redirect ('/products/orders/details?id=' . $order->id);
	}

	$order->status = $_POST['status'];
	if (! $order->put ()) {
		$form->controller->add_notification (__ ('Error updating order status.'));
		$form->controller->redirect ('/products/orders/details?id=' . $order->id);
	}

	// send email receipt
	try {
		$info = $order->orig ();
		$info->user_name = $user->name;

		Mailer::send (array (
			'to' => array ($user->email, $user->name),
			'subject' => 'Status updated on order #' . $order->id,
			'text' => $tpl->render (
				'products/email/update',
				$info
			)
		));
	} catch (Exception $e) {
	}

	$form->controller->add_notification (__ ('Order Status Updated.'));
	$form->controller->redirect ('/products/orders/details?id=' . $order->id);
});
