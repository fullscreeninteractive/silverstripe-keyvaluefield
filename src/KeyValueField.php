<?php

namespace FullscreenInteractive\KeyValueField;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObjectInterface;

class KeyValueField extends CompositeField
{
    /**
     * @var string[]
     */
    protected $keys = 0;


    public function __construct($name, $title = null, $value = null)
    {
        $this->setName($name);
        $this->setTitle($title);

        if ($value) {
            $this->setValue($value);
        }
    }

    public function setKeys(array $keys)
    {
        $this->keys = $keys;
        $this->buildChildren();

        return $this;
    }


    public function HolderID()
    {
        return Convert::raw2att($this->Name);
    }


    public function buildChildren()
    {
        $children = new FieldList();

        $name = $this->name;
        $children->push(LabelField::create($this->name . '__label', $this->title)->addExtraClass('left key__label'));

        if ($this->keys) {
            foreach ($this->keys as $i => $key) {
                $fieldName = sprintf("%s[%s]", $name, $i);
                $value = isset($this->value[$i]) ? $this->value[$i] : '';

                $field = TextField::create($fieldName, $key, $value)
                    ->addExtraClass('key__value');
                $this->invokeWithExtensions('updateKeyValueField', $field, $key, $i);

                $children->push($field);
            }
        }


        $this->setChildren($children);

        return $this;
    }


    public function saveInto(DataObjectInterface $record)
    {
        if ($record->hasMethod('set{$this->name}')) {
            $record->{'set' . $this->name}($this->dataValue());
        } else {
            $record->{$this->name} = $this->dataValue();
        }
    }


    /**
     * Returns the value of the field as a string. If needing the value as an
     * array, use {@link getValue()}.
     *
     * The main difference between this and {@link getValue()} is that this
     * method includes the title of the keys, getValue returns the zero indexed
     * values.
     */
    public function dataValue()
    {
        if ($this->value && is_array($this->value)) {
            $output = [];

            foreach ($this->keys as $i => $key) {
                if (isset($this->value[$i])) {
                    $output[$key] = $this->value[$i];
                }
            }
        }

        return json_encode($output);
    }


    /**
     * Set value of this field.
     *
     * Handles either value as a string (for instance loading from the database)
     * or as an array. The array can either be a simple key/value array, where
     * the key is hash or name of the field
     */
    public function setValue($value, $data = null)
    {
        if ($value) {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }

            if (is_array($value)) {
                $first = array_key_first($value);

                if (is_string($first)) {
                    $this->value = [];

                    foreach ($value as $k => $v) {
                        $i = array_search($k, $this->keys);

                        $this->value[$i] = $v;
                    }
                } else {
                    $this->value = [];

                    foreach ($value as $key => $val) {
                        $this->value[$key] = $val;
                    }
                }
            } else {
                $this->value = [];
            }
        } else {
            $this->value = [];
        }

        foreach ($this->children as $i => $field) {
            if ($field instanceof LabelField) {
                continue;
            }

            $vK = $i - 1;
            $v = isset($this->value[$vK]) ? $this->value[$vK] : '';

            $field->setValue($v, $data);
        }

        return $this;
    }


    public function collateDataFields(&$list, $saveableOnly = false)
    {
        $list[$this->name] = $this;
    }


    public function hasData()
    {
        return true;
    }
}
