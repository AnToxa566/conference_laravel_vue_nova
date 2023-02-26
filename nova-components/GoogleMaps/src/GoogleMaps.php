<?php

declare(strict_types=1);

namespace Custom\GoogleMaps;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\SupportsDependentFields;


class GoogleMaps extends Field
{
    use SupportsDependentFields;

    public $component = 'google-maps';

    public function setDefaultLocation(float $latitude, float $longitude): self
    {
        return $this->withMeta([
            'lat' => $latitude,
            'lng' => $longitude,
        ]);
    }

    public function storeLatitudeField(string $latitude): self
    {
        return $this->withMeta([
            'latitude' => $latitude,
        ]);
    }

    public function storeLongitudeField(string $longitude): self
    {
        return $this->withMeta([
            'longitude' => $longitude,
        ]);
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        if ($request->latitude && $request->longitude) {
            $model->{'latitude'} = $request->latitude;
            $model->{'longitude'} = $request->longitude;
        }
    }
}
