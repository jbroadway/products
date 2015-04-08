/**
 * Basic Javascript-based shopping cart.
 */
var cart = (function ($, Handlebars, accounting) {
	var self = {
			storage_key: 'products_cart',
			contents: {}
		},
		tpl = {
			cart: undefined,
		},
		opts = {
			currency: 'USD',
			currency_symbol: '$',
			prefix: '/products/api',
			button_add: undefined,
			button_update: undefined,
			button_empty: undefined,
			button_continue: undefined,
			button_checkout: undefined,
			show_cart: undefined,
			tpl_cart: undefined
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
	};
	
	return self;
})(jQuery, Handlebars, accounting);
