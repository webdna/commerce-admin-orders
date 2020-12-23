<?php

namespace kuriousagency\commerce\adminorders\elements;

use Craft;
use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Variant as CommerceVariant;
use craft\commerce\elements\Product;
use craft\base\Element;
use kuriousagency\commerce\adminorders\elements\db\VariantQuery;
use craft\elements\db\ElementQueryInterface;

class Variant extends CommerceVariant
{

	public $qty;

	public static function displayName(): string
	{
		return 'Product';
	}

	public function getCpEditUrl(): string
	{
		return '';
	}

	public static function find(): ElementQueryInterface
    {
        return new VariantQuery(static::class);
    }

	/**
    * @inheritdoc
    */
    protected static function defineTableAttributes(): array
    {

		$attributes = parent::defineTableAttributes();

		$attributes['qty'] = Craft::t('commerce', 'Quantity');
		$attributes['stock'] = Craft::t('commerce', 'Stock');

		return $attributes;

	}
	
	
	
	/**
     * @inheritdoc
     */
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
					if (!$this->isAvailable) {
						return '';
					}
					if($this->stock > 0 || $this->hasUnlimitedStock ) {
						$html = '<div class="qty"><input type="text" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="1">';
						$html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add</button></div>';
						
					} else {
						$html = "OOS";
					}
					return $html;
				}

			case 'stock':
				{
					if($this->hasUnlimitedStock) {
						$stock = "∞";
					} else {
						$stock = $this->stock;
					}

					return $stock;
					
				}
			
			case 'price':
				{
					$code = Commerce::getInstance()->getPaymentCurrencies()->getPrimaryPaymentCurrencyIso();
	
					return Craft::$app->getLocale()->getFormatter()->asCurrency($this->$attribute, strtoupper($code));
				}
		
			default:
			{
				return parent::tableAttributeHtml($attribute);
			}
        }

	}


}

?>