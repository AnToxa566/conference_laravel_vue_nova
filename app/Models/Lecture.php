<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'date_time_start',
        'date_time_end',
        'description',
        'presentation_path',
        'presentation_name',
    ];

    public function followingUsers() {
      return $this->belongsToMany(User::class);
    }

    public function user() {
      return $this->belongsTo(User::class);
    }

    public function conference() {
      return $this->belongsTo(Conference::class);
    }

    public function comments() {
      return $this->hasMany(Comment::class);
    }

    public function category() {
      return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeSearch(Builder $query, string $search, int $limit): Builder
    {
        return $query->where('title', 'LIKE', '%'.$search.'%')->limit($limit);
    }
}
