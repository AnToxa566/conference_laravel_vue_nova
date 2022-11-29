<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $table = 'lectures';

    protected $fillable = [
        'user_id',
        'conference_id',

        'title',
        'date_time_start',
        'date_time_end',
        'description',
        'presentation',
    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function conference()
    {
      return $this->belongsTo(Conference::class);
    }

    public function comments()
    {
      return $this->hasMany(Comment::class);
    }
}
