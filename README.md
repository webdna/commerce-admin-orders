# Commerce Admin Orders plugin for Craft CMS 4.x

Commerce Admin Orders is a Craft 4 plugin that allows control panel users to create and update orders.

## Requirements

This plugin requires Craft CMS 4 and Craft Commerce 4.

## Installation

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

        cd /path/to/project

2.  Then tell Composer to load the plugin:

        composer require webdna/commerce-admin-orders

3.  In the Control Panel, go to Settings → Plugins and click the "Install" button for Commerce Admin Orders.

## Commerce Admin Orders Overview

### Creating A New Order

**1. New Order**

Click Commerce in the sidebar and click "New Order" in the top right.

![New Order](resources/screenshots/new-order.png)

**2. Users**

Select a user using one of the following options:

-   Postcode/Zip Code search – search by code and click select
-   Guest Checkout – enter an email address and click continue
-   Existing User - select existing user and click continue

![Users](resources/screenshots/users.png)

**3. Products**

-   Select or search for a product, enter the required quantity and click "Add to cart" (repeat for each required product).
-   Enter any required coupon codes and/or select a different payment currency and click update
-   Ability to enter an estimated country and zipcode.
-   Only shows products that are enabled.
-   Filter products by site if multisite is setup.
-   Click continue

![Products](resources/screenshots/add-products-empty-cart.png)

![Products and cart](resources/screenshots/add-products-cart.png)

**4. Address**

Select an existing address or choose the option to add a new address. Any shipping options can also be selected on this page.

![Address and summary](resources/screenshots/summary.png)

**5. Summary**

For Guest Checkouts, a "Create User Account" option is given. If this option is selected, once the continue button is clicked a user account is created and an activation email is sent to the order email address.

![Address and summary guest](resources/screenshots/summary-new-user.png)

**6. Payment**

Finally, a payment can be processed for the order by clicking "Make Payment" and selecting a payment method.

### Updating An Order (up to version 2.6.6)

Incomplete orders now have extra options to add/remove products, update quantities or add a coupon code.

![Update order](resources/screenshots/update-order.png)

Search for the incomplete order (the best way to do this is to use Craft Commerce’s predefined "Active Carts" and "Inactive Carts" filters) and select.

To add a new product simply select or search for a product, enter the required quantity and click "Add to cart".

To update the quantity of a product, change the line item quantity and click the update button. Likewise, to add a coupon code, enter the code and click the Update button.

Products can be removed by either clicking the X on right of the line item or by entering a quantity of 0 and clicking the Update button.

Brought to you by [webdna](https://webdna.co.uk)
