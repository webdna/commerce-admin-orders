# Commerce Admin Orders Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.7.3 - 2021-10-18

### Fixed

-   Fixed an issue where products were no longer loading due to an update in the way Craft now queries elements - ([#31](https://github.com/KuriousAgency/commerce-admin-orders/issues/31)).

### Updated

-	Country and state dropdowns now only show enabled options ([#30](https://github.com/KuriousAgency/commerce-admin-orders/issues/30)).

## 2.7.2 - 2021-03-30

### Fixed

-	Fixed a bug where the Orders sort menu triggered the new order modal ([#29](https://github.com/KuriousAgency/commerce-admin-orders/issues/29)).

## 2.7.1 - 2020-12-23

### Changed

-   Qty field defaults to 1 - thanks @bartrylant

### Fixed

-	MySQL error when sql_mode=only_full_group_by was set
-   PHP notice when an existing user wasn't selected

## 2.7.0 - 2020-12-22

### Changed

-	Updated to support Craft 3.5 and Commerce 3
-   Removed the edit order feature as Commerce 3's standard order editing can now be used.
-   Updated requirements to Craft CMS 3.5 and Commerce 3.2.0

## 2.6.6 - 2020-03-31

### Fixed

-	google apikey check fix

### Changed

-	Downgraded requirements to Craft 3.3

## 2.6.5 - 2020-03-04

### Fixed

-   added a check to see if the google apikey is defined.

## 2.6.4 - 2020-02-21

### Fixed

-   Fixed a bug where products weren't displaying for some custom purchasables

## 2.6.3 - 2020-02-19

-   Changelog update

## 2.6.2 - 2020-02-19

### Changed

-   Craft 3.4 compatability updates
-   Updated purchasable pricing format
-   Updated requirements to Craft 3.4

## 2.6.1 - 2020-01-09

### Added

-   shipping options hook
-   shipping extras hook
-   Added estimated shipping
-   Only show enabled products for each site.

## 2.5.5 - 2019-12-02

### Added

-   Added support for Google's autocomplete address

### Fixed

-   Support for verbb tickets fix

## 2.5.4 - 2019-10-30

### Fixed

-   Fixed add to cart bug when using Craft Commerce 2.2

## 2.5.3 - 2019-09-25

### Fixed

-   added orderId param when adding to cart.

## 2.5.2 - 2019-09-24

### Fixed

-   css namespace modal to fix issue #12

### Changed

-   Create empty order object if no order exists.
-   On Add to cart check if \$cart is found by either Number or Id.

## 2.5.1 - 2019-09-15

### Fixed

-   Events Support

### Changed

-   plugin icon

## 2.5.0 - 2019-08-29

### Added

-   Verbb Events Ticket support
-   Verbb Gift Vouchers Support

## 2.4.2 - 2019-07-17

### Fixed

-   cpUrl including orderNumber param

## 2.4.1 - 2019-07-16

### Fixed

-   Address validation

## 2.4.0 - 2019-06-18

### Added

-   Digital Products support

### Fixed

-   css padding for element index on order edit page

## 2.3.1 - 2019-06-12

### Changed

-   Elements no longer have cp edit urls.
-   Showing site menu in element index.

## 2.3.0 - 2019-06-04

### Added

-   Bundles support

## 2.2.10 - 2019-05-02

### Fixed

-   removed all hard-coded CP triggers

## 2.2.9 - 2019-02-25

### Fixed

-   removed the hard-coded CP trigger
