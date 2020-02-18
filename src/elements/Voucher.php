<?php

namespace kuriousagency\commerce\adminorders\elements;

use Craft;
use craft\commerce\Plugin as Commerce;
use verbb\giftvoucher\elements\Voucher as CommerceVoucher;

class Voucher extends CommerceVoucher
{

	public $qty;

	public static function refHandle()
	{
		return 'voucher';
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

?>