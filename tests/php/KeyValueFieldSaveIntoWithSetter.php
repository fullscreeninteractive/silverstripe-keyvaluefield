<?php

namespace FullscreenInteractive\KeyValueField\Tests;

use SilverStripe\ORM\DataObject;

/**
 * @internal
 */
class KeyValueFieldSaveIntoWithSetter extends DataObject
{
    private static $table_name = 'KeyValueFieldSaveIntoWithSetter';

    /**
     * @var mixed
     */
    protected $QuantityStore = null;

    public function setQuantity($v): void
    {
        $this->QuantityStore = $v;
    }

    /**
     * @return mixed
     */
    public function getStoredQuantity()
    {
        return $this->QuantityStore;
    }
}
