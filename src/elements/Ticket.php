<?php

namespace kuriousagency\commerce\adminorders\elements;

use Craft;
use craft\commerce\Plugin as Commerce;
use verbb\events\elements\Ticket as CommerceTicket;

class Ticket extends CommerceTicket
{

	public $qty;

	public function getCpEditUrl(): string
	{
		return '';
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
					if(($this->quantity != null && $this->quantity > 0) || ($this->event->capacity != null && $this->event->capacity > 0) || $this->event->capacity == null){
						$html = '<div class="qty"><input type="text" name="adminOrderQty['.$this->id.']" class="text adminOrderQty" value="1">';
						$html .= ' <button class="btn submit atc" data-id="'.$this->id.'">Add</button></div>';
						
					} else {
						$html = "OOS";
					}
					//Craft::dd($this);
					return $html;
				}

			case 'stock':
				{
					return $this->availableQuantity() > 1000 ? '1000+' : $this->availableQuantity();
					
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