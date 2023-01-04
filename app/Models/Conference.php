<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function announcers() {
        return $this->belongsToMany(User::class)->where('type', '=', UserConsts::ANNOUNCER);
    }

    public function listeners() {
        return $this->belongsToMany(User::class)->where('type', '=', UserConsts::LISTENER);
    }

    public function lectures() {
      return $this->hasMany(Lecture::class);
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeSearch(Builder $query, string $search, int $limit): Builder
    {
        return $query->where('title', 'LIKE', '%'.$search.'%')->limit($limit);
    }
}
