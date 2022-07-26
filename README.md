# SilverStripe Key Value Field

[![Version](http://img.shields.io/packagist/v/fullscreeninteractive/silverstripe-keyvaluefield.svg)](https://packagist.org/packages/fullscreeninteractive/silverstripe-keyvaluefield)
[![License](http://img.shields.io/packagist/l/fullscreeninteractive/silverstripe-keyvaluefield.svg)](license.md)

A reusable approach to a form field which extends a simple Text field to have
several named parts (keys). This module also supports User Defined Forms.

## Installation

```
composer require fullscreeninteractive/silverstripe-keyvaluefield
```

## Usage

![Image of Function](https://raw.githubusercontent.com/fullscreeninteractive/silverstripe-keyvaluefield/master/client/img/demo.png)

```php
use FullscreenInteractive\KeyValueField\KeyValueField;

$fields = new FieldList(
    KeyValueField::create('Quantity', 'Enter quantity of each size')
        ->setKeys([
            'Small',
            'Medium',
            'Large'
        ])
);
```

When using ORM managed forms and models (i.e `saveInto`) data will be saved as
a serialized array of the values to each of the keys. You can change this
behaviour if needed in your `Form` class.

```php
public function doSave($data, $form)
{
    $quantity = $form->dataFieldByName('Quantity');

    // returns an array of key => value
    $values = json_decode($quantity->dataValue(), true);

    echo $values['Small']
}
```

## Licence

BSD 3-Clause License
