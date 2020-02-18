<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\commerce\adminorders\controllers;

use kuriousagency\commerce\adminorders\AdminOrders;

use Craft;
use craft\web\Controller;
use craft\commerce\elements\Order;
use craft\commerce\Plugin as Commerce;
use craft\commerce\models\Customer;
use craft\commerce\models\Address;
use craft\helpers\UrlHelper;

use craft\elements\User;

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

	public function actionNewOrder($cart=null)
	{
		
		Commerce::getInstance()->getCarts()->forgetCart();
		Commerce::getInstance()->getCustomers()->forgetCustomer();
		$order = Commerce::getInstance()->getCarts()->getCart(true);
		$variables = [
			'order' => $order,
			'cart' => $cart
		];
		
		return $this->renderTemplate('commerce-admin-orders/user', $variables);
	}
	
	public function actionProducts($cart=null)
	{

		$number = Craft::$app->getRequest()->getParam('orderNumber');

		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);
		if ($order->currency != $order->paymentCurrency) {
			$order->currency = $order->paymentCurrency;
			Craft::$app->getElements()->saveElement($order, false);
			$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);
		}

		$variables = [
			'order' => $order,
			'cart' => $cart,
			'purchasableTypes' => AdminOrders::$plugin->purchasables->getAllTypes(),
		];
		//Craft::dd($variables);

		return $this->renderTemplate('commerce-admin-orders/products', $variables);
	}

	public function actionUser()
	{
		$number = Craft::$app->getRequest()->getBodyParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$variables = [
			'order' => $order,
		];

		return $this->renderTemplate('commerce-admin-orders/user', $variables);
	}

	public function actionAddress()
	{
		$number = Craft::$app->getRequest()->getParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$variables = [
			'order' => $order,
		];

		return $this->renderTemplate('commerce-admin-orders/address', $variables);
	}

	public function actionSummary()
	{
		$number = Craft::$app->getRequest()->getParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$variables = [
			'order' => $order,
		];

		return $this->renderTemplate('commerce-admin-orders/summary', $variables);
	}



	public function actionUpdateUser()
	{
		$number = Craft::$app->getRequest()->getParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);
		
		// If there isn't a current order we need to create a fresh element
		if (!$order) {
			$order = new Order();
		}

		$userId = Craft::$app->getRequest()->getBodyParam('userId')[0];
		$email = Craft::$app->getRequest()->getBodyParam('email');

		$previousGuestCustomer = false;

		if(Craft::$app->getRequest()->getBodyParam('orderId')) {
			$orderId = Craft::$app->getRequest()->getBodyParam('orderId');
			$customerOrder = Commerce::getInstance()->getOrders()->getOrderById($orderId);

			if($customerOrder) {
				$customer = Commerce::getInstance()->getCustomers()->getCustomerById($customerOrder->customerId);
				if($customer->userId) {
					$userId = $customer->userId;
				} else {
					$email = $customerOrder->email;
					
					$customerOrderShippingAddress = new Address($customerOrder->shippingAddress->toArray([
						'id',
						'attention',
						'title',
						'firstName',
						'lastName',
						'countryId',
						'stateId',
						'address1',
						'address2',
						'city',
						'zipCode',
						'phone',
						'alternativePhone',
						'businessName',
						'businessTaxId',
						'businessId',
						'stateName'
					]
					));
					$customerOrderShippingAddress->id = null;
					if (Commerce::getInstance()->getAddresses()->saveAddress($customerOrderShippingAddress, false)) {
						$order->setShippingAddress($customerOrderShippingAddress);
					}

					$customerOrderBillingAddress = new Address($customerOrder->billingAddress->toArray([
						'id',
						'attention',
						'title',
						'firstName',
						'lastName',
						'countryId',
						'stateId',
						'address1',
						'address2',
						'city',
						'zipCode',
						'phone',
						'alternativePhone',
						'businessName',
						'businessTaxId',
						'businessId',
						'stateName'
					]
					));

					$customerOrderBillingAddress->id = null;
					if (Commerce::getInstance()->getAddresses()->saveAddress($customerOrderBillingAddress, false)) {
						$previousGuestCustomer = true;
						$order->setBillingAddress($customerOrderBillingAddress);
					}
				}
			}

		}

		// user checkout
		if ($userId) {
			
			$user = Craft::$app->users->getUserById($userId);
			$customer = Commerce::getInstance()->getCustomers()->getCustomerByUserId($user->id);

			if($customer) {

				if ($email = $customer->getEmail()) {
					$order->setEmail($email);
				}
				if($customer->primaryBillingAddressId){
					$createNewAddress = false;
				}

			} else {
				$customer = new Customer();
				$customer->setUser($user);
				$email = $user->email;
				$order->setEmail($user->email);

				Commerce::getInstance()->getCustomers()->saveCustomer($customer);
			} 
		// guest checkout
		} else {

			$customer = new Customer();
			Commerce::getInstance()->getCustomers()->saveCustomer($customer);
			$order->setEmail($email);

		}

		$order->customerId = $customer->id;
		$order = $this->_cloneOrder($order,$email,$previousGuestCustomer);

		$variables = [
			'order' => $order,
		];

		//Craft::dd($order);

		Craft::$app->getUrlManager()->setRouteParams([
            'order' => $order
        ]);

		return $this->redirectToPostedUrl($order);
	}

	public function actionGetUsersByZipCode()
	{
		$users = [];
		
		$zipCode = Craft::$app->getRequest()->getParam('zipCode');
		$order['number'] = Craft::$app->getRequest()->getParam('orderNumber');

		if($zipCode) {
			$users = AdminOrders::getInstance()->users->findUsersByZipCode($zipCode);
		}

		$variables = [
			'zipCode' => $zipCode,
			'order' => $order,
			'users' => $users,
		];
		return $this->renderTemplate('commerce-admin-orders/user', $variables);

	}

	public function actionUpdateAddress()
	{
		$number = Craft::$app->getRequest()->getParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$shippingAddressSelect = Craft::$app->getRequest()->getBodyParam('shippingAddressSelect');
		$billingAddressSelect = Craft::$app->getRequest()->getBodyParam('billingAddressSelect');
		$shippingMethod = Craft::$app->getRequest()->getBodyParam('shippingMethod');
		$shippingMethodHandle = Craft::$app->getRequest()->getParam('shippingMethodHandle');

		if ($shippingAddressSelect > 0) {
			$shippingAddress = Commerce::getInstance()->getAddresses()->getAddressByIdAndCustomerId($shippingAddressSelect, $order->customerId);
			$order->setShippingAddress($shippingAddress);
			//Craft::$app->getElements()->saveElement($order, false);
		}

		if ($billingAddressSelect > 0) {
			$billingAddress = Commerce::getInstance()->getAddresses()->getAddressByIdAndCustomerId($billingAddressSelect, $order->customerId);
			$order->setBillingAddress($billingAddress);
			//Craft::$app->getElements()->saveElement($order, false);
		}

		//redirect if value = 0 => new address
		if ($shippingAddressSelect === '0') {
			return $this->redirect('commerce-admin-orders/orders/address/new?type=shipping&orderNumber='.$order->number);
		}

		if ($billingAddressSelect === '0') {
			return $this->redirect('commerce-admin-orders/orders/address/new?type=billing&orderNumber='.$order->number);
		}

		// Set Shipping method on cart.
		if ($shippingMethodHandle) {
			$order->shippingMethodHandle = $shippingMethodHandle;
			//Craft::$app->getElements()->saveElement($order, false);
		}

		$order->setFieldValuesFromRequest('fields');
		
		Craft::$app->getElements()->saveElement($order, false);

		return $this->redirectToPostedUrl($order);
	}

	public function actionRegisterUser()
	{	
		
		$request = Craft::$app->getRequest();
		$orderId = $request->getParam('orderId');

		if($request->getParam('registerUser')) {

			$email = $request->getParam('email');
			
			$user = Craft::$app->getUsers()->getUserByUsernameOrEmail($email);

			if(!$user) {
				$user = new User();
				$user->pending = true;
				$user->firstName = $request->getParam('firstName');
				$user->lastName = $request->getParam('lastName');
				$user->email = $request->getParam('email');
				$user->username = $request->getParam('email');

				Craft::$app->getElements()->saveElement($user, false);

				Craft::$app->getUsers()->sendActivationEmail($user);
			}

		}

		return $this->redirect(UrlHelper::cpUrl('commerce/orders/'.$orderId));
		
	}

	public function actionNewAddress()
	{
		$number = Craft::$app->getRequest()->getParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$type = Craft::$app->getRequest()->getParam('type');
		$shippingAddress = Craft::$app->getRequest()->getParam('shipping');
		$billingAddress = Craft::$app->getRequest()->getParam('billing');
		$settings = AdminOrders::$plugin->getSettings();

		if ($type) {
			$model = new Address();
			if ($type == 'shipping') {
				$model = (!$order->shippingAddressId && $order->estimatedShippingAddressId) ? $order->estimatedShippingAddress : $model;
			}
			return $this->renderTemplate('commerce-admin-orders/address-new', [
				'order' => $order,
				'type' => $type,
				'model' => $model,
				'settings' => $settings,
			]);
		}
		if ($shippingAddress) {
			$order->validate();
			//Craft::dd($order->shippingAddress->getErrors());
			return $this->renderTemplate('commerce-admin-orders/address-new', [
				'order' => $order,
				'type' => 'shipping',
				'model' => $order->shippingAddress,
				'settings' => $settings,
			]);
		}
	}

	public function actionSaveAddress()
	{
		$number = Craft::$app->getRequest()->getParam('orderNumber');
		$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);

		$shippingAddress = Craft::$app->getRequest()->getParam('shipping');
		$billingAddress = Craft::$app->getRequest()->getParam('billing');
		
		if ($shippingAddress) {
			$order->setShippingAddress($shippingAddress);
	
			if (!$order->validate() || !Craft::$app->getElements()->saveElement($order, false)) {
				Craft::$app->getUrlManager()->setRouteParams([
					'order' => $order,
					'model' => $order->shippingAddress,
				]);
				return null;
			}
		}
		if ($billingAddress) {
			$order->setBillingAddress($billingAddress);

			if (!$order->validate() || !Craft::$app->getElements()->saveElement($order, false)) {
				Craft::$app->getUrlManager()->setRouteParams([
					'order' => $order,
					'model' => $order->billingAddress,
				]);
				return null;
			}
		}

		return $this->redirectToPostedUrl($order);
	}


	public function actionSaveOrder()
	{
		$this->requireAcceptsJson();
		
		$email = Craft::$app->getRequest()->getParam('email');
		$userId = Craft::$app->getRequest()->getParam('userId');
		$createNewAddress = true;

		$order = new Order();
		$order->number = Commerce::getInstance()->getCarts()->generateCartNumber();
		$order->lastIp = Craft::$app->getRequest()->userIP;
        $order->orderLanguage = Craft::$app->language;
		$order->currency = Commerce::getInstance()->getPaymentCurrencies()->getPrimaryPaymentCurrencyIso();

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

	public function actionAddToCart()
	{

		$this->requireAcceptsJson();
		$request = Craft::$app->getRequest();

		if($number = $request->getParam('orderNumber')) {
			$cart = Commerce::getInstance()->getOrders()->getOrderByNumber($number);
		} elseif($orderId = $request->getParam('orderId')) {
			$cart = Commerce::getInstance()->getOrders()->getOrderById($orderId);
		}

		if (!$cart) {
			return $this->asErrorJson([
				'message' => 'Cart not defined',
			]);
		}
		
		if ($purchasableId = $request->getParam('purchasableId')) {

			//Craft::dd($purchasableId);
            $note = '';
            $options = [];
			$qty = (int)$request->getParam('adminOrderQty', 1);

			$lineItem = Commerce::getInstance()->getLineItems()->resolveLineItem($cart->id, $purchasableId, $options);

			
            // New line items already have a qty of one.
            if ($lineItem->id) {
                $lineItem->qty += $qty;
            } else {
                $lineItem->qty = $qty;
            }

            $lineItem->note = $note;

			$cart->addLineItem($lineItem);
		}

		Craft::$app->getElements()->saveElement($cart, false);
		// save again so lineitems have ids so adjustment can be correctly shown against them
		Craft::$app->getElements()->saveElement($cart, false);
		
		return $this->asJson([
			'success' => true,
			'html' => $this->getView()->renderTemplate('commerce-admin-orders/cart', [
				'order' => $cart
			])
		]);
		
	}

	public function actionGetAdminVariants()
	{
		return $this->asJson([
			'success' => true,
			'html' => $this->getView()->renderTemplate('commerce-admin-orders/admin-variant')
		]);

	}

	public function actionGetControls()
	{
		$request = Craft::$app->getRequest();
		$number = $request->getParam('orderNumber');
		$id = $request->getParam('orderId');
		if (!$number && !$id) {
			return $this->asJson(['success'=>false,'message'=>'nor id or number']);
		}
		if ($number) {
			$order = Commerce::getInstance()->getOrders()->getOrderByNumber($number);
		}
		if ($id) {
			$order = Commerce::getInstance()->getOrders()->getOrderById($id);
		}
		
		return $this->asJson([
			'success' => true,
			'html' => $this->getView()->renderTemplate('commerce-admin-orders/partials/controls', ['order'=>$order])
		]);

	}

	public function actionGetOrderNumberById()
	{

		$id = Craft::$app->getRequest()->getParam('orderId');
		
		$order = Commerce::getInstance()->getOrders()->getOrderById($id);

		if($order) {

			return $this->asJson([
				'success' => true,
				'orderNumber' => $order->number
			]);

		}

		return false;
	}

	private function _cloneOrder($order, $email, $previousGuestCustomer)
	{
		
		$clone = new Order();
		$clone->number = Commerce::getInstance()->getCarts()->generateCartNumber();
		$clone->lastIp = Craft::$app->getRequest()->userIP;
        $clone->orderLanguage = Craft::$app->language;
		$clone->currency = $order->paymentCurrency;
		$clone->paymentCurrency = $order->paymentCurrency;
		$clone->couponCode = $order->couponCode;
		$clone->gatewayId = $order->gatewayId;
		$clone->customerId = $order->customerId;
		$clone->setEmail($email);

		if($previousGuestCustomer) {
			$clone->billingAddressId = $order->billingAddressId;
			$clone->shippingAddressId = $order->shippingAddressId;
		}

		Craft::$app->getElements()->saveElement($clone, false);

		foreach($order->lineItems as $lineItem)
		{
			$item = Commerce::getInstance()->getLineItems()->createLineItem($clone->id, $lineItem->purchasableId, $lineItem->options, $lineItem->qty, $lineItem->note);
			$clone->addLineItem($item);
		}

		Craft::$app->getElements()->saveElement($clone, false);
		Commerce::getInstance()->getCarts()->forgetCart();
		if ($order->id) {
			Craft::$app->getElements()->deleteElement($order);
		}

		return $clone;
	}
}
