<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    use HasFactory;

    protected $table = 'zoom_meetings';

    protected $fillable = [
        'id',
        'lecture_id',

        'uuid',
        'host_id',
        'topic',
        'type',
        'timezone',
        'start_time',

        'start_url',
        'join_url',
    ];

    public function lecture() {
        return $this->belongsTo(Lecture::class);
    }
}
