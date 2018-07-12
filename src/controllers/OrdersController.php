<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\commerceadminorders\controllers;

use kuriousagency\commerceadminorders\CommerceAdminOrders;

use Craft;
use craft\web\Controller;
use craft\commerce\elements\Order;
use craft\commerce\Plugin as Commerce;
use craft\commerce\models\Customer;
use craft\commerce\models\Address;

/**
 * @author    Kurious Agency
 * @package   CommerceAdminOrders
 * @since     1.0.0
 */
class OrdersController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================

	public function actionSaveOrder()
	{
		$this->requireAcceptsJson();
		
		$email = Craft::$app->getRequest()->getParam('email');
		$userId = Craft::$app->getRequest()->getParam('userId');
		/*$users = Craft::$app->getRequest()->getParam('users');
		if($users){
			$userId = $users[0];
		}*/
		
		$createNewAddress = true;

		$order = new Order();
		$order->number = Commerce::getInstance()->getCarts()->generateCartNumber();
		$order->lastIp = Craft::$app->getRequest()->userIP;
        $order->orderLanguage = Craft::$app->language;
		$order->currency = Commerce::getInstance()->getPaymentCurrencies()->getPrimaryPaymentCurrencyIso();

		//$orderStatus = Commerce::getInstance()->getOrderStatuses()->getOrderStatusByHandle('incomplete');
		//$order->orderStatusId = $orderStatus->id;
		if($userId){
			$user = Craft::$app->users->getUserById($userId);
			$customer = Commerce::getInstance()->getCustomers()->getCustomerByUserId($user->id);

			if($customer){
				if ($email = $customer->getEmail()) {
					$order->setEmail($email);
				}
				if($customer->primaryBillingAddressId){
					$createNewAddress = false;
				}

			}else{
				$customer = new Customer();
				$customer->setUser($user);

				Commerce::getInstance()->getCustomers()->saveCustomer($customer);
			}
			
		}else{
			$customer = new Customer();
			Commerce::getInstance()->getCustomers()->saveCustomer($customer);
			$order->setEmail($email);
		}

		$order->customerId = $customer->id;

		if($createNewAddress){
			$address = new Address();
			$address->firstName = 'No address';
			$order->setShippingAddress($address);

			$address = new Address();
			$address->firstName = 'No address';
			$order->setBillingAddress($address);
		}

		if(Craft::$app->getElements()->saveElement($order, false)){
			return $this->asJson(['success' => true, 'orderId' => $order->id]);
		}

		return $this->asErrorJson(Craft::t('commerce', 'Could not create a new order'));
	}
}
