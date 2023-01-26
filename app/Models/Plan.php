<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Plan extends Model
{
    use HasFactory;

    /**
     * The name of the basic plan.
     *
     * @var string
     */
    public const BASIC_PLAN = 'basic';

    /**
     * The name of the beginner plan.
     *
     * @var string
     */
    public const BEGINNER_PLAN = 'beginner';

    /**
     * The name of the profession plan.
     *
     * @var string
     */
    public const PROFESSION_PLAN = 'profession';

    /**
     * The name of the unlimited plan.
     *
     * @var string
     */
    public const UNLIMITED_PLAN = 'unlimited';

    /**
     * The name of the table in the database.
     *
     * @var string
     */
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'stripe_price',
        'price',
        'joins',
        'description',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
