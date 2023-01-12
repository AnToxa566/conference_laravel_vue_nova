<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'lecture_id',

        'description',
    ];

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }

    public function lecture(): BelongsTo
    {
      return $this->belongsTo(Lecture::class);
    }

    /**
     * Interact with the comment's username.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function userName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->user->first_name . ' ' . $this->user->last_name
        );
    }
}
