; <?php /*

[Products]

; This is the title of your products page (/products).

title = Products

; This is the layout to use for the products page.

layout = default

; The payment handler for product purchases.

payment_handler = ""

; Whether to use the web server's built-in file sending
; for faster downloads. Requires the appropriate web
; server extension to be enabled.

xsendfile = ""

; Email address to notify when new orders are placed.

notify = ""

; Whether to include this app in the list of pages
; available to the Tools > Navigation tree.

include_in_nav = On

[Admin]

handler = products/admin
name = Products
install = products/install
upgrade = products/upgrade
version = 0.9.0

; */ ?>