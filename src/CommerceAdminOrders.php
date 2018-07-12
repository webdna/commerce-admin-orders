<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\commerceadminorders;

use kuriousagency\commerceadminorders\services\Orders as OrdersService;
use kuriousagency\commerceadminorders\assetbundles\ordersnew\OrdersNewAsset;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\base\Element;
use craft\events\ModelEvent;
use craft\web\View;
use craft\events\TemplateEvent;

use yii\base\Event;

/**
 * Class CommerceAdminOrders
 *
 * @author    Kurious Agency
 * @package   CommerceAdminOrders
 * @since     1.0.0
 *
 * @property  OrdersService $orders
 */
class CommerceAdminOrders extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CommerceAdminOrders
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
		self::$plugin = $this;
		
		$this->setComponents([
			'orders' => OrdersService::class,
		]);

		if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(OrdersNewAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
		}

		Event::on(
			Element::class,
			Element::EVENT_BEFORE_SAVE,
			function(ModelEvent $event){
				if(get_class($event->sender) == 'craft\\commerce\\elements\\Order'){
					$products = Craft::$app->getRequest()->getBodyParam('addProduct');
					if($products){
						$event->sender = $this->orders->addProducts($event->sender, $products);
					}

					$qty = Craft::$app->getRequest()->getBodyParam('qty');
					if($qty){
						$event->sender = $this->orders->updateQty($event->sender, $qty);
					}
				}
			}
		);

        /*Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'commerce-admin-orders/orders';
            }
        );*/

        /*Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'commerce-admin-orders/orders/do-something';
            }
        );*/

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'commerce-admin-orders',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
