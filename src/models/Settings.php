<?php
/**
 * Commerce Admin Orders plugin for Craft CMS 3.x
 *
 * Create a new commerce order from the admin
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2018 webdna
 */

namespace webdna\commerce\adminorders\models;

use webdna\commerce\adminorders\AdminOrders;

use Craft;
use craft\base\Model;

/**
 * @author    webdna
 * @package   AdminOrders
 * @since     1.1.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var ?string
     */
    public ?string $googleApiKey = null;

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        $rules = [];
        return $rules;
    }
}
