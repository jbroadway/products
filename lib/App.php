<?php

namespace products;

class App {
	/**
	 * Fetch a list of payment handlers.
	 */
	public static function payment_handlers () {
		$files = glob ('apps/*/conf/payments.php');
		$files = is_array ($files) ? $files : array ();
		$providers = array ();
		foreach ($files as $file) {
			$ini = parse_ini_file ($file);
			if (! is_array ($ini)) {
				continue;
			}
			$providers = array_merge ($providers, $ini);
		}
		asort ($providers);
		return $providers;
	}
	
	/**
	 * Fetch discount, if available. Returns it as a number out of 100,
	 * or 0 if there is no discount.
	 */
	public static function discount () {
		$cb = \Appconf::products ('Products', 'discount_callback');
		if (is_callable ($cb)) {
			return call_user_func ($cb);
		}
		return 0;
	}
	
	/**
	 * Fetch the "allow invoice" option, if available. Returns true or false.
	 * If true, members of that type can be invoiced for their purchase separately
	 * and bypass the payment processing to complete their order immediately.
	 */
	public static function allow_invoice () {
		$cb = \Appconf::products ('Products', 'allow_invoice_callback');
		if (is_callable ($cb)) {
			return call_user_func ($cb);
		}
		return false;
	}
}

?>