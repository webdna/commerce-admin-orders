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

/**
 * @author    webdna
 * @package   CommerceAdminOrders
 * @since     1.0.0
 */
class Purchasables extends Component
{
    // Public Methods
    // =========================================================================

    public function getAllTypes(): array
    {
        $types = [];
        foreach(Commerce::getInstance()->getPurchasables()->getAllPurchasableElementTypes() as $type)
        {
            $elementType = preg_replace('/^[\w\\\]+elements/', 'webdna\\commerce\\adminorders\\elements', $type);
            if (class_exists($elementType)) {
                $handle = $elementType::refHandle();
                $types[$handle] = [
                    'url' => "#$handle"."Tab",
                    'label' => $elementType::displayName().'s',
                    'elementType' => $elementType,
                ];
            }
        }

        return $types;
    }

}
