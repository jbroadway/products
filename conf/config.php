; <?php /*

[Products]

; This is the title of your products page (/products).

title = Products

; This is the layout to use for the products page.

layout = default

; The payment handler for product purchases.

payment_handler = ""

; A callback to check for available discounts for the current user's
; membership type, specified as a percentage discount.
discount_callback = ""

; A callback to check whether an "invoice me" option should be available
; for payments. The site owner will receive an email of the order that
; they will manually invoice for.
allow_invoice_callback = ""

; Whether to use the web server's built-in file sending
; for faster downloads. Requires the appropriate web
; server extension to be enabled.

xsendfile = ""

; Email address to notify when new orders are placed.

notify = ""

; Whether to include this app in the list of pages
; available to the Tools > Navigation tree.

include_in_nav = On

; Maximum amount to charge for shipping.

max_shipping = Off

; Amount after which shipping is free.

shipping_free_over = Off

[Admin]

handler = products/admin
name = Products
install = products/install
upgrade = products/upgrade
version = 0.9.2

; */ ?>