<?php

namespace FullscreenInteractive\KeyValueField;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\UserForms\Model\EditableFormField;

if (!class_exists(EditableFormField::class)) {
    return;
}

class EditableKeyValueField extends EditableFormField
{
    private static $singular_name = 'Key Value Field';

    private static $plural_name = 'Key Value Fields';

    private static $db = [
        'Keys' => 'Text',
        'IsNumeric' => 'Boolean',
    ];

    private static $table_name = 'EditableKeyValueField';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', TextareaField::create('Keys')->setDescription('One key per line'));
            $fields->addFieldsToTab('Root.Main', CheckboxField::create('IsNumeric', 'Validate field values as numeric values?')->setDescription('Validates that all the values are numeric'));
        });

        return parent::getCMSFields();
    }


    public function getFormField()
    {
        $field = KeyValueField::create($this->Name, $this->Title ?: false);

        if ($this->IsNumeric) {
            $field->setValueFieldClass(NumericField::class);
            $field->setFieldCallback(function ($field) {
                $field->setHTML5(true);
            });
        }

        $field->setKeys($this->getKeysAsArray());

        $this->doUpdateFormField($field);

        return $field;
    }


    public function getValueFromData($data)
    {
        $incoming = isset($data[$this->Name]) ? $data[$this->Name] : [];
        $value = [];

        foreach ($this->getKeysAsArray() as $i => $k) {
            if (isset($incoming[$i])) {
                $value[$k] = $incoming[$i];
            } else {
                $value[$k] = '';
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


    public function getSubmittedFormField()
    {
        return SubmittedKeyValueFormField::create();
    }
}
