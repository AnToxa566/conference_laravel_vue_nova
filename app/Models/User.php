<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;


class User extends Authenticatable
{
    use Billable, HasApiTokens, HasFactory, Notifiable;


    public const ADMIN = 'Admin';

    public const LISTENER = 'Listener';

    public const ANNOUNCER = 'Announcer';


    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'type',
        'email',
        'joins_left',
        'phone_number',
        'password',
        'birthdate',
        'country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'email_verified_at' => 'datetime',
    ];


    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function favoriteLectures(): BelongsToMany
    {
      return $this->belongsToMany(Lecture::class);
    }

    public function lectures(): HasMany
    {
      return $this->hasMany(Lecture::class);
    }

    public function comments(): HasMany
    {
      return $this->hasMany(Comment::class);
    }
}
