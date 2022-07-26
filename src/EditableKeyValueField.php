<?php

namespace FullscreenInteractive\KeyValueField;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\UserForms\Model\EditableFormField;

if (class_exists(EditableFormField::class)) {
    return;
}

class EditableKeyValueField extends EditableFormField
{
    private static $singular_name = 'Key Value Field';

    private static $plural_name = 'Key Value Fields';

    private static $db = [
        'Keys' => 'Text'
    ];

    private static $table_name = 'EditableKeyValueField';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', TextareaField::create('Keys')->setDescription('One key per line'));
        });

        return parent::getCMSFields();
    }


    public function getFormField()
    {
        $field = KeyValueField::create($this->Name, $this->Title ?: false)
            ->setKeys($this->getKeysAsArray());

        $this->doUpdateFormField($field);

        return $field;
    }


    public function getValueFromData($data)
    {
        $incoming = isset($data[$this->Name]) ? $data[$this->Name] : false;

        if (!$incoming) {
            return json_encode([]);
        }

        $value = [];

        foreach ($this->getKeysAsArray() as $i => $k) {
            if (isset($data[$i])) {
                $value[$k] = $data[$i];
            }
        }

        return json_encode($value);
    }


    public function getKeysAsArray()
    {
        return array_map(function($k) {
            return trim($k);
        }, explode(PHP_EOL, $this->Keys));
    }
}
