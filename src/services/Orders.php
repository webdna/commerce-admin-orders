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
class Orders extends Component
{
    // Public Methods
    // =========================================================================

	public function addProducts(Order $order, array $products)
	{
		foreach($products as $product)
		{
			$lineItem = Commerce::getInstance()->getLineItems()->resolveLineItem($order->id, $product, [], 1, '');
			$order->addLineItem($lineItem);
		}

		return $order;
	}

	public function updateQty(Order $order, array $lineItems)
	{
		foreach($lineItems as $id => $qty)
		{
			$lineItem = Commerce::getInstance()->getLineItems()->getLineItemById($id);

			if(!$lineItem || ($order->id != $lineItem->orderId)){
				throw new NotFoundHttpException('Line item not found');
			}

			$lineItem->qty = $qty;

			if($qty == 0){
				$order->removeLineItem($lineItem);
			}else{
				$order->addLineItem($lineItem);
			}
		}

		return $order;
	}

}
