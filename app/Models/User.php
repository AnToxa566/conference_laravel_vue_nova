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

    /**
     * User Type - Admin.
     *
     * @var string
     */
    public const ADMIN = 'Admin';

    /**
     * User Type - Listener.
     *
     * @var string
     */
    public const LISTENER = 'Listener';

    /**
     * User Type - Announcer.
     *
     * @var string
     */
    public const ANNOUNCER = 'Announcer';

    /**
     * The name of the table in the database.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'type',
        'email',
        'phone_number',
        'password',
        'birthdate',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
