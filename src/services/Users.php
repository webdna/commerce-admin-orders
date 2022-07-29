<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2018 webdna
 */

namespace webdna\commerce\adminorders\services;

use webdna\commerce\adminorders\AdminOrders;

use Craft;
use craft\base\Component;
use craft\commerce\Plugin as Commerce;
use craft\commerce\models\Customer;
use craft\commerce\elements\Order;
use craft\db\Query;

/**
 * @author    webdna
 * @package   CommerceAdminOrders
 * @since     1.0.0
 */
class Users extends Component
{
    // Public Methods
    // =========================================================================

    public function findUsersByZipCode(string $zipCode): array
    {
        $zipCode = str_replace(" ",'',$zipCode);

            $rows = (new Query())
            ->select([
                'MAX(orders.id) AS id',
                'orders.email',
                'addresses.firstName',
                'addresses.lastName',
                'addresses.address1',
                'addresses.city',
                'addresses.zipCode',
                'customers.userId'
            ])
            ->from('{{%commerce_orders}} orders')
            ->innerJoin('{{%commerce_addresses}} addresses', '[[addresses.id]] = [[orders.shippingAddressId]]')
            ->innerJoin('{{%commerce_customers}} customers', '[[customers.id]] = [[orders.customerId]]')
            ->where(['REPLACE(addresses.zipCode," ","")' => $zipCode])
            ->orWhere(['LIKE','REPLACE(addresses.zipCode," ","")',$zipCode])
            ->andWhere(['orders.iscompleted'=>1])
            ->groupBy('orders.email')
            ->limit(20)
            ->all();

        $users = [];

        foreach ($rows as $row) {
            $users[] = $row;
        }

        return $users;
    }
}
