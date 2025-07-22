<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'description',
    ];

    public static function create(array $seedCategory) {}

    public function jokes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {

        return $this->belongsToMany(Joke::class);
    }

    public function jokesByTitleDesc(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->jokes()->orderBy('title', 'desc');
    }

    public function jokesByDateAddedDesc(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->jokes()->orderBy('created_at', 'desc');
    }
}
