<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2018 webdna
 */

namespace webdna\commerce\adminorders;

use webdna\commerce\adminorders\services\Orders as OrdersService;
use webdna\commerce\adminorders\services\Users as UsersService;
use webdna\commerce\adminorders\services\Purchasables as PurchasablesService;
use webdna\commerce\adminorders\assetbundles\neworder\NewOrder;
use webdna\commerce\adminorders\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\base\Element;
use craft\base\Model;
use craft\events\ModelEvent;
use craft\web\View;
use craft\events\TemplateEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\UserPermissions;

use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Class CommerceAdminOrders
 *
 * @author    webdna
 * @package   CommerceAdminOrders
 * @since     1.1.0
 *
 * @property  OrdersService $orders
 */
class AdminOrders extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Plugin
     */
    public static Plugin $plugin;

    // Public Properties
    // =========================================================================

    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $settings = $this->getSettings();

        $this->setComponents([
            'orders' => OrdersService::class,
            'users' => UsersService::class,
            'purchasables' => PurchasablesService::class,
        ]);

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    if (Craft::$app->user->checkPermission('accessPlugin-commerce-admin-orders')) {
                        try {
                            Craft::$app->getView()->registerAssetBundle(NewOrder::class);
                        } catch (InvalidConfigException $e) {
                            Craft::error(
                                'Error registering AssetBundle - '.$e->getMessage(),
                                __METHOD__
                            );
                        }
                    }
                }
            );
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['commerce-admin-orders/orders/new'] = 'commerce-admin-orders/orders/new-order';
                $event->rules['commerce-admin-orders/orders/products'] = 'commerce-admin-orders/orders/products';
                $event->rules['commerce-admin-orders/orders/user'] = 'commerce-admin-orders/orders/user';
                $event->rules['commerce-admin-orders/orders/address'] = 'commerce-admin-orders/orders/address';
                $event->rules['commerce-admin-orders/orders/address/new'] = 'commerce-admin-orders/orders/new-address';
                $event->rules['commerce-admin-orders/orders/summary'] = 'commerce-admin-orders/orders/summary';
                $event->rules['commerce-admin-orders/orders/save'] = 'commerce-admin-orders/orders/save-order';
            }
        );

        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[] = [
                'heading' => 'Admin Orders',
                'permissions' => [
                    'accessPlugin-commerce-admin-orders' => ['label' => 'Admin Orders'],
                ]
            ];
        });

        // Craft::$app->view->hook('cp.commerce.order.edit.main-pane', function(array &$context) {
        // 	$view = Craft::$app->getView();
        // 	$order = $context['order'];

        // 	if ($order->isCompleted) {
        // 		return '';
        // 	}

        // 	$content = $view->renderTemplate('commerce-admin-orders/partials/controls', ['order'=>$order, 'showCurrency' => false]);
        // 	$content .= $view->renderTemplate('commerce-admin-orders/admin-variant', ['order'=>$order, 'showTabs'=>true, 'purchasableTypes'=>$this->purchasables->getAllTypes()]);
        // 	return $content;
        // });

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
    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml() : ?string
    {
        $settings = $this->getSettings();
        return Craft::$app->getView()->renderTemplate('commerce-admin-orders/settings', [
            'settings' => $settings,
        ]);
    }


}
