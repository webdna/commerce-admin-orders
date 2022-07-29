<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2018 webdna
 */

namespace webdna\commerce\adminorders\assetbundles\neworder;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    webdna
 * @package   CommerceAdminOrders
 * @since     1.0.0
 */
class NewOrder extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = "@webdna/commerce/adminorders/assetbundles/neworder/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Modal.js',
            'js/Edit.js',
        ];

        $this->css = [
            'css/Modal.css',
            //'css/Edit.css',
        ];

        parent::init();
    }
}
