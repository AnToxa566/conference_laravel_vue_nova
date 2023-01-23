<?php

declare(strict_types=1);

namespace Custom\GoogleMaps;

use Laravel\Nova\Fields\Field;

class GoogleMaps extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'google-maps';

    /**
     * Set the set location.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return self
     */
    public function setDefaultLocation(float $latitude, float $longitude): self
    {
        return $this->withMeta([
            'lat' => $latitude,
            'lng' => $longitude,
        ]);
    }

    /**
     * Set the latitude field name.
     *
     * @param string $latitude
     *
     * @return self
     */
    public function storeLatitudeField(string $latitude): self
    {
        return $this->withMeta([
            'latitude' => $latitude,
        ]);
    }

    /**
     * Set the longitude field name.
     *
     * @param string $longitude
     *
     * @return self
     */
    public function storeLongitudeField(string $longitude): self
    {
        return $this->withMeta([
            'longitude' => $longitude,
        ]);
    }
}
