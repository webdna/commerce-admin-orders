<?php

namespace kuriousagency\commerce\adminorders\elements;

use Craft;
use craft\digitalproducts\elements\Product as CommerceProduct;

class Product extends CommerceProduct
{

	public $qty;

	public static function refHandle()
	{
		return 'digital';
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
        $attributes[] = 'qty';

        return $attributes;
	}
	
	protected function tableAttributeHtml(string $attribute): string
    {

		switch ($attribute) {
			case 'qty':
				{
					$html = '<div class="qty"><input type="text" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="">';
					$html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add</button></div>';

					return $html;
				}
		
			default:
			{
				return parent::tableAttributeHtml($attribute);
			}
        }

	}


}

?>