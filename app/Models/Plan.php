<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Plan extends Model
{
    use HasFactory;


    public const BASIC_PLAN = 'basic';

    public const BEGINNER_PLAN = 'beginner';

    public const PROFESSION_PLAN = 'profession';

    public const UNLIMITED_PLAN = 'unlimited';


    protected $table = 'plans';

    protected $fillable = [
        'slug',
        'stripe_price',
        'price',
        'joins',
        'description',
    ];


    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
