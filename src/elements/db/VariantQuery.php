<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace kuriousagency\commerce\adminorders\elements\db;

use Craft;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\commerce\Plugin;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use yii\db\Connection;
use craft\commerce\elements\db\VariantQuery as CommerceVariantQuery;


class VariantQuery extends CommerceVariantQuery
{
    

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        

		$this->_applyHasProductParam();
        
		
		//Craft::dd($this->siteId);

        return parent::beforePrepare();
    }

    protected function afterPrepare(): bool
    {
        $this->subQuery->orWhere(['elements.type'=>'craft\\commerce\\elements\\Variant']);

        return parent::afterPrepare();
    }

    /**
     * Applies the hasVariant query condition
     */
    private function _applyHasProductParam()
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
