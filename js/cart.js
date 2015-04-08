/**
 * Basic Javascript-based shopping cart.
 */
var cart = (function ($, accounting) {
	var self = {
			storage_key: 'products_cart',
			contents: {}
		},
		opts = {
			currency: 'USD',
			currency_symbol: '$',
			prefix: '/products/api',
			button_add: '#cart-add',
			show_cart: '#cart-contents'
		};
	
	/**
	 * Add item to cart. Note: Price should include
	 * cents, e.g., 1900 instead of 19.00, whichc conforms
	 * to Stripe's pricing format.
	 */
	self.add = function(id, label, price, qty) {
		self.contents[id] = {id: id, label: label, price: price, qty: qty};
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
	 */
	self.format = function (price) {
		return accounting.formatMoney (
			price / 100,
			opts.currency_symbol
		);
	};

	/**
	 * Serialize cart object to JSON and store via jStorage.
	 */
	self.serialize = function () {
		$.jStorage.set (self.storage_key, JSON.stringify (self.contents));
	};
	
	/**
	 * Event handler for add to cart buttons.
	 */
	self.add_handler = function (e) {
		e.preventDefault ();
		
		var $btn = $(e.target),
			id = $btn.data ('id'),
			label = $btn.data ('label'),
			price = $btn.data ('price'),
			qty = $btn.data ('quantity') || 1;
		
		self.add (id, label, price, qty);
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
		
		// Attach events
		
	};
	
	return self;
})(jQuery, accounting);
