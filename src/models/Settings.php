<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\commerce\adminorders\models;

use kuriousagency\commerce\adminorders\AdminOrders;

use Craft;
use craft\base\Model;

/**
 * @author    Kurious Agency
 * @package   AdminOrders
 * @since     1.1.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */

	public $googleApiKey = null;

    // Public Methods
	// =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }
}
