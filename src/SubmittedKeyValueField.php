<?php

namespace FullscreenInteractive\KeyValueField;

use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;

class SubmittedKeyValueFormField extends SubmittedFormField
{
    private static $table_name = 'SubmittedKeyValueFormField';


    public function getFormattedValue()
    {
        $data = json_decode($this->Value, true);
        $output = '';

        foreach ($data as $key => $value) {
            $output .= '<strong>'. $key .'</strong>: '. $value .'<br>';
        }

        return DBField::create_field('HTMLText', $output);
    }


    /**
     * Return the value of this submitted form field suitable for inclusion
     * into the CSV
     *
     * @return DBField
     */
    public function getExportValue()
    {
        return $this->getFormattedValue();
    }
}
