<?php

/**
 * This is the settings form for the blog app.
 */

$this->require_admin ();

require_once ('apps/admin/lib/Functions.php');

$page->layout = 'admin';
$page->title = __ ('Product Settings');

$form = new Form ('post', $this);

$form->data = array (
	'title' => $appconf['Products']['title'],
	'layouts' => admin_get_layouts (),
	'layout' => $appconf['Products']['layout'],
	'payment_handler' => $appconf['Products']['payment_handler'],
	'payment_handlers' => products\App::payment_handlers (),
	'xsendfile' => $appconf['Products']['xsendfile'],
	'notify' => $appconf['Products']['notify']
);

echo $form->handle (function ($form) {
	$settings = Appconf::merge ('products', array (
		'Products' => array (
			'title' => $_POST['title'],
			'layout' => $_POST['layout'],
			'payment_handler' => $_POST['payment_handler'],
			'xsendfile' => $_POST['xsendfile'],
			'notify' => $_POST['notify']
		)
	));

	if (! Ini::write ($settings, 'conf/app.products.' . ELEFANT_ENV . '.php')) {
		printf ('<p>%s</p>', __ ('Unable to save changes. Check your folder permissions and try again.'));
		return;
	}

	$form->controller->add_notification (__ ('Settings saved.'));
	$form->controller->redirect ('/products/admin');
});

?>