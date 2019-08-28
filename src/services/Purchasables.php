<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\commerce\adminorders\services;

use kuriousagency\commerce\adminorders\AdminOrders;

use Craft;
use craft\base\Component;
use craft\commerce\Plugin as Commerce;
use craft\commerce\models\Customer;
use craft\commerce\elements\Order;

/**
 * @author    Kurious Agency
 * @package   CommerceAdminOrders
 * @since     1.0.0
 */
class Purchasables extends Component
{
    // Public Methods
    // =========================================================================

	public function getAllTypes()
	{
		$types = [];
		foreach(Commerce::getInstance()->getPurchasables()->getAllPurchasableElementTypes() as $type)
		{
			$elementType = preg_replace('/^[\w\\\]+elements/', 'kuriousagency\\commerce\\adminorders\\elements', $type);
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
