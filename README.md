# Simple products page for the Elefant CMS

This app provides a simple single-page product listing with sales handled
through the [Stripe Payments](https://github.com/jbroadway/stripe) app.

#### To do:

* Inventory tracking
* Shopping cart

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
