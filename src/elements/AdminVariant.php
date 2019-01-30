<?php

namespace kuriousagency\commerce\adminorders\elements;

use Craft;
use craft\commerce\elements\Variant;

class AdminVariant extends Variant
{

	public $qty;

	/**
    * @inheritdoc
    */
    protected static function defineTableAttributes(): array
    {

		$attributes = parent::defineTableAttributes();

		$attributes['qty'] = Craft::t('commerce', 'Quantity');

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
					if($this->stock > 0) {
						$html = '<input type="text" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="">';
						$html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add to cart</button>';
						
					} else {
						$html = "OOS";
					}
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