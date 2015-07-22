# Products is a simple shopping cart for the Elefant CMS

**Status: Beta**

This app provides a simple shopping cart for the Elefant CMS, with sales handled
through the [Stripe Payments](https://github.com/jbroadway/stripe) app.

## Current features

* Shopping cart
* Basic inventory tracking
* Configurable taxes
* Shipping fees with max amount and "free over $x" settings
* Digital downloads
* Stripe payment processing
* Basic order management
* Discounts and "invoice me" payment option [available via callbacks](#callbacks-for-discounts-and-invoice-me-payment-option)

## Features yet to be added

* Admin sales dashboard
* Inventory running low/out admin notifications
* Shipment tracking
* Promo codes

## Installation

First, you will need to install the [Stripe Payments](https://github.com/jbroadway/stripe) app.

Once you've installed that, the easiest way to install the app is:

1. Log into Elefant as a site admin
2. Go to Tools > Designer > Install App/Theme
3. Paste the following link and click Install:

```
https://github.com/jbroadway/products/archive/master.zip
```

Alternately, you can run the following from the command line:

```bash
cd /path/to/your/site
./elefant install https://github.com/jbroadway/products/archive/master.zip
```

## Usage

### Configure the app

In the Elefant admin area, go to Tools > Products > Settings to customize the app
for your site. Also visit Tools > Products > Taxes and Tools > Products > Categories
to set up your taxes and product categories.

### Adding the products page to your site

In the Elefant admin area, go to Tools > Navigation and drag the "Products" page into
your site tree. Note that if you've renamed the page in the Products app settings, the
page will be renamed in the site navigation as well.

### Callbacks for discounts and "invoice me" payment option

The following settings can be set in your `conf/app.products.config.php` configuration
file like this:

```ini
; A callback to check for available discounts for the current user's
; membership type, specified as a percentage discount.
discount_callback = "\myapp\Callbacks::discount"

; A callback to check whether an "invoice me" option should be available
; for payments. The site owner will receive an email of the order that
; they will manually invoice for.
allow_invoice_callback = "\myapp\Callbacks::allow_invoice"
```

Here is an example class that implements basic examples of callbacks for the two settings:

```php
<?php // apps/myapp/lib/Callbacks.php

namespace myapp;

use User;

class Callbacks {
	/**
	 * If user is logged in, allow invoice.
	 */
	public static function allow_invoice () {
		return User::is_valid ();
	}
	
	/**
	 * Give everyone 10% off.
	 */
	public static function discount () {
		return User::is_valid () ? 10 : 0;
	}
}

```

In the above example, it allows all users who are logged in to request an invoice,
and also gives them 10% off. From here, you can fill in any logic around different
member types that works for your site's needs.
