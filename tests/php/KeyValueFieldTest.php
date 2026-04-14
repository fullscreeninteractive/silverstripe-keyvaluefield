<?php

namespace FullscreenInteractive\KeyValueField\Tests;

use FullscreenInteractive\KeyValueField\KeyValueField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;

class KeyValueFieldTest extends SapphireTest
{
    protected $usesDatabase = false;

    public function testSetKeysBuildsValueFields(): void
    {
        $field = KeyValueField::create('Sizes', 'Pick sizes')
            ->setKeys(['Small', 'Medium', 'Large']);

        $children = $field->FieldList();
        $this->assertGreaterThanOrEqual(4, $children->count());

        $valueFields = array_values(array_filter($children->toArray(), function ($child) {
            return !$child instanceof LabelField;
        }));

        $this->assertCount(3, $valueFields);
        $this->assertInstanceOf(TextField::class, $valueFields[0]);
        $this->assertSame('Small', $valueFields[0]->Title());
    }

    public function testDataValueEncodesKeyedJson(): void
    {
        $field = KeyValueField::create('Qty', 'Quantities')
            ->setKeys(['A', 'B'])
            ->setValue(['A' => '1', 'B' => '2']);

        $this->assertSame('{"A":"1","B":"2"}', $field->dataValue());
    }

    public function testDataValueReturnsEmptyObjectJsonWhenUnset(): void
    {
        $field = KeyValueField::create('Qty', 'Quantities')
            ->setKeys(['A', 'B']);

        $this->assertSame('[]', $field->dataValue());
    }

    public function testSetValueFromJsonString(): void
    {
        $field = KeyValueField::create('Qty', 'Quantities')
            ->setKeys(['Small', 'Large'])
            ->setValue('{"Small":"1","Large":"2"}');

        $this->assertSame('{"Small":"1","Large":"2"}', $field->dataValue());
    }

    public function testSetValueFieldClassAndCallback(): void
    {
        $field = KeyValueField::create('N', 'Numbers')
            ->setValueFieldClass(NumericField::class)
            ->setFieldCallback(function ($f) {
                $f->setHTML5(true);
            })
            ->setKeys(['One']);

        $children = $field->FieldList()->toArray();
        $numeric = null;
        foreach ($children as $child) {
            if ($child instanceof NumericField) {
                $numeric = $child;
                break;
            }
        }

        $this->assertInstanceOf(NumericField::class, $numeric);
        $this->assertTrue($numeric->getHTML5());
    }

    public function testSaveIntoAssignsPropertyWhenNoSetter(): void
    {
        $field = KeyValueField::create('Quantity', 'Qty')
            ->setKeys(['Small'])
            ->setValue(['Small' => '5']);

        $record = KeyValueFieldSaveIntoTarget::create();
        $field->saveInto($record);

        $this->assertSame('{"Small":"5"}', $record->getField('Quantity'));
    }

    public function testSaveIntoUsesSetterWhenPresent(): void
    {
        $field = KeyValueField::create('Quantity', 'Qty')
            ->setKeys(['Small'])
            ->setValue(['Small' => '3']);

        $record = KeyValueFieldSaveIntoWithSetter::create();
        $field->saveInto($record);

        $this->assertSame('{"Small":"3"}', $record->getStoredQuantity());
    }
}
