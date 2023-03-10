<?php

declare(strict_types=1);

namespace Custom\PhoneNumber;

use Laravel\Nova\Fields\Field;

class PhoneNumber extends Field
{
    public $component = 'phone-number';

    public function storeCountryPhoneCode(string $countryPhoneCodeAttribute): self
    {
        return $this->withMeta([
            'countryPhoneCodeAttribute' => $countryPhoneCodeAttribute,
        ]);
    }
}
