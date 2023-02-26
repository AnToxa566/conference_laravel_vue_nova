<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Country extends Model
{
    use HasFactory;


    protected $table = 'countries';

    protected $fillable = [
        'name',
        'short_code',
        'phone_code',
    ];
}
