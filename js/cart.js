/**
 * Basic Javascript-based shopping cart.
 */
var cart = (function ($, Handlebars, accounting) {
	var self = {
			storage_key: 'products_cart',
			order_key: 'products_order',
			contents: {}
		},
		tpl = {
			cart: undefined,
			checkout: undefined
		},
		opts = {
			order: undefined,
			post: false,
			currency: 'USD',
			currency_symbol: '$',
			prefix: '/products/api/',
			taxes: [],
			max_shipping: false,
			shipping_free_over: false,
			button_add: undefined,
			button_update: undefined,
			button_empty: undefined,
			button_continue: undefined,
			button_checkout: undefined,
			show_cart: undefined,
			show_checkout: undefined,
			tpl_cart: undefined,
			tpl_checkout: undefined
		};
	
	/**
	 * Add item to cart. Note: Price should include
	 * cents, e.g., 1900 instead of 19.00, whichc conforms
	 * to Stripe's pricing format.
	 */
	self.add = function(id, label, url, price, qty) {
		self.contents[id] = {id: id, label: label, url: url, price: price, qty: qty};
		self.serialize ();
	};
	
	/**
	 * Remove item from cart.
	 */
	self.remove = function (id) {
		delete self.contents[id];
		self.serialize ();
	};
	
	/**
	 * Update item quantity.
	 */
	self.update = function (id, qty) {
		self.contents[id].qty = qty;
		self.serialize ();
	};
	
	/**
	 * Clear the cart contents.
	 */
	self.clear = function () {
		self.contents = {};
		self.serialize ();
	};
	
	/**
	 * Calculate subtotal.
	 */
	self.subtotal = function () {
		var sub = 0;
		for (var i in self.contents) {
			sub += self.contents[i].price * self.contents[i].qty;
		}
		return sub;
	};
	
	/**
	 * Format a price for display.
	 * Note: Requires accounting.js.
	 *
	 * Also used as the `money` Handlebars helper:
	 *
	 *     {{money price}}
	 */
	self.format = function (price) {
		return accounting.formatMoney (
			price / 100,
			opts.currency_symbol
		);
	};
	
	/**
	 * Handlebars helper for outputting a formatted subtotal.
	 *
	 *     {{format_subtotal}}
	 */
	self.format_subtotal = function () {
		return self.format (self.subtotal ());
	};

	/**
	 * Serialize cart object to JSON and store via jStorage.
	 */
	self.serialize = function () {
		$.jStorage.set (self.storage_key, JSON.stringify (self.contents));
	};
	
	/**
	 * Show the shopping cart by rendering its template.
	 */
	self.show_cart = function () {
		if (opts.show_cart !== undefined) {
			$(opts.show_cart).html (tpl.cart (self));
		}
	};
	
	/**
	 * Event handler for add to cart buttons.
	 */
	self.add_handler = function (e) {
		e.preventDefault ();
		
		var $btn = $(e.target),
			id = $btn.data ('id'),
			label = $btn.data ('label'),
			url = $btn.data ('url'),
			price = $btn.data ('price'),
			qty = $btn.data ('quantity') || 1;
		
		self.add (id, label, url, price, qty);
		location.href = '/products/cart';
	};
	
	/**
	 * Event handler for updating cart quantities.
	 */
	self.update_handler = function (e) {
		e.preventDefault ();
		
		$('.cart-input-qty').each (function () {
			var $this = $(this),
				id = $this.data ('id'),
				qty = $this.val ();
			
			self.update (id, qty);
		});
		
		self.show_cart ();
	};
	
	/**
	 * Event handler for clearing the cart contents.
	 */
	self.empty_handler = function (e) {
		e.preventDefault ();
		
		self.clear ();
		self.show_cart ();
	};
	
	/**
	 * Event handler for continue browsing.
	 */
	self.continue_handler = function (e) {
		e.preventDefault ();
		
		location.href = '/products';
	};
	
	/**
	 * Event handler for checkout.
	 */
	self.checkout_handler = function (e) {
		e.preventDefault ();
		
		location.href = '/products/checkout';
	};
	
	/**
	 * Returns a list of item IDs.
	 */
	self.item_ids = function () {
		var ids = [];
		for (var id in self.contents) {
			ids.push (id);
		}
		return ids;
	};
	
	/**
	 * Returns taxes as an array of objects
	 * with the tax_name and tax_amount calculated
	 * based on opts.taxes and the provided items
	 * list.
	 */
	self.taxes = function (items, shipping) {
		var taxes = [];

		for (var tax in opts.taxes) {
			var name = opts.taxes[tax].name,
				percent = parseInt (opts.taxes[tax].percent),
				amt = 0;

			for (var i in items) {
				for (var t in items[i].taxes) {
					if (items[i].taxes[t] === name) {
						amt += parseInt (items[i].price) * self.contents[items[i].id].qty * (percent / 100)
					}
				}
			}
			
			if (opts.taxes[tax].charge_on_shipping) {
				amt += shipping * (percent / 100);
			}

			if (amt > 0) {
				taxes.push ({
					tax_name: name,
					tax_amount: amt
				});
			}
		}

		return taxes;
	};

	/**
	 * Returns the shipping amount calculated based
	 * on the max_shipping and shipping_free_over
	 * settings and the provided items list.
	 */
	self.shipping = function (items, subtotal) {
		if (opts.shipping_free_over !== undefined && opts.shipping_free_over > 0 && subtotal >= opts.shipping_free_over) {
			return 0;
		}

		var amt = 0;

		for (var i in items) {
			amt += parseInt (items[i].shipping) * self.contents[items[i].id].qty;
		}

		if (opts.max_shipping !== undefined && opts.max_shipping > 0 && amt > opts.max_shipping) {
			amt = opts.max_shipping;
		}

		return amt;
	};
	
	/**
	 * Add the tax amounts from the provided tax list
	 * returned by self.taxes ().
	 */
	self.add_taxes = function (taxes) {
		var total = 0;
		for (var i in taxes) {
			total += parseInt (taxes[i].tax_amount);
		}
		return total;
	};
	
	/**
	 * Callback from checkout_info API call.
	 */
	self.checkout = function (res) {
		if (! res.success) {
			$(opts.show_checkout).html ('<p>An unknown error occurred. Please try again later.</p>');
			return;
		}

		if (opts.show_checkout === undefined) {
			$(opts.show_checkout).html ('<p>Checkout is not yet configured for this site.</p>');
			return;
		}
		
		var subtotal = self.subtotal (),
			shipping = self.shipping (res.data, subtotal);

		var data = {
			subtotal: subtotal,
			shipping: shipping,
			taxes: self.taxes (res.data, shipping),
			total: 0
		};

		data.total = data.subtotal + data.shipping + self.add_taxes (data.taxes);

		$(opts.show_checkout).html (tpl.checkout (data));
	};
	
	/**
	 * Initialize the plugin.
	 */
	self.init = function (options) {
		opts = $.extend ({}, opts, options);

		// Auto-adjust EUR and GBP currency symbols
		if (options.currency_symbol === undefined) {
			if (opts.currency === 'GBP') {
				opts.currency_symbol = '£';
			} else if (opts.currency === 'EUR') {
				opts.currency_symbol = '€';
			}
		}

		if (opts.max_shipping !== undefined) {
			opts.max_shipping = parseInt (opts.max_shipping);
		}

		if (opts.shipping_free_over !== undefined) {
			opts.shipping_free_over = parseInt (opts.shipping_free_over);
		}
		
		// Initialize cart from storage
		var stored = $.jStorage.get (self.storage_key);
		if (stored) {
			self.contents = $.parseJSON (stored);
		} else {
			self.serialize ();
		}
		
		// Compile, configure and render handlebars template
		if (opts.tpl_cart !== undefined) {
			tpl.cart = Handlebars.compile ($(opts.tpl_cart).html ());
		}
		
		if (opts.tpl_checkout !== undefined) {
			tpl.checkout = Handlebars.compile ($(opts.tpl_checkout).html ());
		}
		
		Handlebars.registerHelper ('money', self.format);
		Handlebars.registerHelper ('subtotal', self.format_subtotal);
		
		self.show_cart ();
		
		// Attach events
		if (opts.button_add !== undefined) {
			$(opts.button_add).on ('click', self.add_handler);
		}
		
		if (opts.button_update !== undefined) {
			$(opts.button_update).on ('click', self.update_handler);
		}
		
		if (opts.button_empty !== undefined) {
			$(opts.button_empty).on ('click', self.empty_handler);
		}
		
		if (opts.button_continue !== undefined) {
			$(opts.button_continue).on ('click', self.continue_handler);
		}
		
		if (opts.button_checkout !== undefined) {
			$(opts.button_checkout).on ('click', self.checkout_handler);
		}
		
		if (opts.show_checkout !== undefined) {
			$.get (opts.prefix + 'checkout_info', {items: self.item_ids ()}, self.checkout);
		}
	};
	
	return self;
})(jQuery, Handlebars, accounting);
