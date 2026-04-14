<?php

namespace FullscreenInteractive\KeyValueField\Tests;

use SilverStripe\ORM\DataObject;

/**
 * @internal
 */
class KeyValueFieldSaveIntoTarget extends DataObject
{
    private static $table_name = 'KeyValueFieldSaveIntoTarget';

    private static $db = [
        'Quantity' => 'Text',
    ];
}
