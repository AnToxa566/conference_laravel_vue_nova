<?php

declare(strict_types=1);

namespace Custom\GoogleMaps;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\SupportsDependentFields;


class GoogleMaps extends Field
{
    use SupportsDependentFields;


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

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        if ($request->latitude && $request->longitude) {
            $model->{'latitude'} = $request->latitude;
            $model->{'longitude'} = $request->longitude;
        }
    }
}
