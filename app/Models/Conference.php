<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\UserConsts;

class Conference extends Model
{
    use HasFactory;

    protected $table = 'conferences';

    protected $fillable = [
        'title',
        'date_time_event',
        'latitude',
        'longitude',
        'country',
        'category_id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function announcers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->where('type', '=', UserConsts::ANNOUNCER);
    }

    public function listeners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->where('type', '=', UserConsts::LISTENER);
    }

    public function lectures(): HasMany
    {
      return $this->hasMany(Lecture::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeSearch(Builder $query, string $search, int $limit): Builder
    {
        return $query->where('title', 'LIKE', '%'.$search.'%')->limit($limit);
    }

    public function scopeBeforeEvent(Builder $query): Builder
    {
        return $query->whereDate('date_time_event', '>=', date('Y-m-d'));
    }
}
