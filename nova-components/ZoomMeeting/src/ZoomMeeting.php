<?php

declare(strict_types=1);

namespace Custom\ZoomMeeting;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;

use App\Models\ZoomMeeting as ZoomMeetingModel;

class ZoomMeeting extends Field
{
    use SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'zoom-meeting';

    public function zoomMeeting(ZoomMeetingModel $zoomMeeting): self
    {
        return $this->withMeta([
            'zoomMeeting' => $zoomMeeting,
        ]);
    }
}
