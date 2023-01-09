<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function childs(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class, 'category_id', 'id');
    }

    public function lectures(): HasMany
    {
        return $this->hasMany(Lecture::class, 'category_id', 'id');
    }
}
