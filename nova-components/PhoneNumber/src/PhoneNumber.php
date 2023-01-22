<?php

declare(strict_types=1);

namespace Custom\PhoneNumber;

use Laravel\Nova\Fields\Field;

class PhoneNumber extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'phone-number';

    public function storeCountryPhoneCode(string $countryPhoneCodeAttribute): PhoneNumber
    {
        return $this->withMeta([
            'countryPhoneCodeAttribute' => $countryPhoneCodeAttribute,
        ]);
    }
}
