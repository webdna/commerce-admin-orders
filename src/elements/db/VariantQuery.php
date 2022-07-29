<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace webdna\commerce\adminorders\elements\db;

use Craft;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\commerce\Plugin;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use yii\db\Connection;
use craft\commerce\elements\db\VariantQuery as CommerceVariantQuery;
use craft\commerce\elements\db\ProductQuery;


class VariantQuery extends CommerceVariantQuery
{
    // Protected Methods
    // =========================================================================

    protected function beforePrepare(): bool
    {
        $this->_applyHasProductParam();

        return parent::beforePrepare();
    }

    /**
     * Applies the hasVariant query condition
     */
    private function _applyHasProductParam(): void
    {
        //if ($this->hasProduct) {
            if ($this->hasProduct instanceof ProductQuery) {
                $productQuery = $this->hasProduct;
            } else {
                $query = Product::find();
                $productQuery = Craft::configure($query, []);
            }

            $productQuery->siteId = $this->siteId;
            $productQuery->status = 'enabled';
            $productQuery->limit = null;
            $productQuery->select('commerce_products.id');
            $productIds = $productQuery->column();

            // Remove any blank product IDs (if any)
            $productIds = array_filter($productIds);

            $this->subQuery->andWhere(['in', 'commerce_products.id', $productIds]);
        //}
    }
}
