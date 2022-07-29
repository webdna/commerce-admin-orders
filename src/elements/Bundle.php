<?php

namespace webdna\commerce\adminorders\elements;

use Craft;
use craft\commerce\Plugin as Commerce;
use webdna\commerce\bundles\elements\Bundle as CommerceBundle;

class Bundle extends CommerceBundle
{

    public int $qty;
    public int $stock;

    public static function refHandle(): string
    {
        return 'bundle';
    }

    public function getCpEditUrl(): string
    {
        return '';
    }

    protected static function defineTableAttributes(): array
    {
        $attributes = parent::defineTableAttributes();
        $attributes['qty'] = Craft::t('commerce', 'Quantity');
        $attributes['stock'] = Craft::t('commerce', 'Stock');

        return $attributes;
    }

    protected static function defineActions(string $source = null): array
    {
        $actions = [];
        return $actions;
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        $attributes = [];

        $attributes[] = 'title';
        // $attributes[] = 'product';
        $attributes[] = 'sku';
        $attributes[] = 'price';
        $attributes[] = 'stock';
        $attributes[] = 'qty';

        return $attributes;
    }

    protected function tableAttributeHtml(string $attribute): string
    {

        switch ($attribute) {
            case 'qty':
                {
                    if($this->hasStock()) {
                        $html = '<div class="qty"><input type="number" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="1">';
                        $html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add</button></div>';

                    } else {
                        $html = "OOS";
                    }
                    return $html;
                }

            case 'stock':
                {
                    //if($this->hasUnlimitedStock) {
                        $stock = "∞";
                    //} else {
                        //$stock = $this->stock;
                    //}

                    return $this->getStock() > 1000 ? '1000+' : $this->getStock();

                }

            case 'price':
                {
                    $code = $code = Commerce::getInstance()->getPaymentCurrencies()->getPrimaryPaymentCurrencyIso();

                    return Craft::$app->getLocale()->getFormatter()->asCurrency($this->$attribute, strtoupper($code));
                }

            default:
            {
                return parent::tableAttributeHtml($attribute);
            }
        }

    }

}
