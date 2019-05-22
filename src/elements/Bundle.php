<?php

namespace kuriousagency\commerce\adminorders\elements;

use Craft;
use kuriousagency\bundles\elements\Bundle as CommerceBundle;

class Bundle extends CommerceBundle
{

	public $qty;
	public $stock;

	/**
    * @inheritdoc
    */
    protected static function defineTableAttributes(): array
    {

		$attributes = parent::defineTableAttributes();

		$attributes['qty'] = Craft::t('commerce', 'Quantity');

		return $attributes;

	}
	
	protected static function defineActions(string $source = null): array
    {
		$actions = [];
		return $actions;
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
					//if($this->stock > 0 || $this->hasUnlimitedStock ) {
						$html = '<div class="qty"><input type="text" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="">';
						$html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add</button></div>';
						
					//} else {
					//	$html = "OOS";
					//}
					return $html;
				}

			case 'stock':
				{
					//if($this->hasUnlimitedStock) {
						$stock = "Unlimited ";
					//} else {
						//$stock = $this->stock;
					//}

					return $stock;
					
				}
		
			default:
			{
				return parent::tableAttributeHtml($attribute);
			}
        }

	}


}

?>