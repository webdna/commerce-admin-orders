<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace kuriousagency\commerce\adminorders\elements\db;

use Craft;
use craft\base\Element;
use craft\commerce\Plugin;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use yii\db\Connection;
use verbb\events\elements\db\TicketQuery as EventsTicketQuery;


class TicketQuery extends EventsTicketQuery
{

    // Protected Methods
    // =========================================================================


    protected function afterPrepare(): bool
    {
        $this->subQuery->orWhere(['elements.type'=>'verbb\\events\\elements\\Ticket']);

        return parent::afterPrepare();
    }

}
