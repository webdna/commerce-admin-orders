<?php

namespace webdna\commerce\adminorders\elements;

use Craft;
use craft\commerce\Plugin as Commerce;
use craft\digitalproducts\elements\Product as CommerceProduct;

class Product extends CommerceProduct
{

    public int $qty;

    public static function refHandle(): string
    {
        return 'digital';
    }

    protected static function defineTableAttributes(): array
    {
        $attributes = parent::defineTableAttributes();

        $attributes['qty'] = Craft::t('commerce', 'Quantity');
        $attributes['stock'] = Craft::t('commerce', 'Stock');

        return $attributes;
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        $attributes = [];

        $attributes[] = 'title';
        // $attributes[] = 'product';
        $attributes[] = 'sku';
        $attributes[] = 'price';
        $attributes[] = 'qty';

        return $attributes;
    }

    protected function tableAttributeHtml(string $attribute): string
    {
        switch ($attribute) {
            case 'qty':
                {
                    $html = '<div class="qty"><input type="number" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="1">';
                    $html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add</button></div>';

                    return $html;
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
