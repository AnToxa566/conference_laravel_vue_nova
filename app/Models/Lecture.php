<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $table = 'lectures';

    protected $fillable = [
        'user_id',
        'conference_id',
        'category_id',

        'title',
        'description',

        'date_time_start',
        'date_time_end',

        'presentation_path',
        'presentation_name',

        'is_online',
    ];

    protected $casts = [
        'date_time_start' => 'datetime',
        'date_time_end' => 'datetime',
    ];

    public function followingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function zoomMeeting(): HasOne
    {
        return $this->hasOne(ZoomMeeting::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeSearch(Builder $query, string $search, int $limit): Builder
    {
        return $query->where('title', 'LIKE', '%'.$search.'%')->limit($limit);
    }

    public function scopeBeforeConferenceEvent(Builder $query): Builder
    {
        return $query->whereHas('conference', function($q) {
            $q->whereDate('date_time_event', '>=', date('Y-m-d'));
        });
    }

    public function scopeWhereTimeBetween(Builder $query, string $fieldName, string $fromTime, string $toTime): Builder
    {
        return $query->whereTime($fieldName, '>=', $fromTime)->whereTime($fieldName, '<=', $toTime);
    }

    public function scopeWhereBetweenTimes(Builder $query, string $fromTime, string $toTime): Builder
    {
        return $query->whereTime('date_time_start', '<=', $fromTime)->whereTime('date_time_end', '>=', $toTime);
    }
}
