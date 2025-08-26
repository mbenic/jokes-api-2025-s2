<?php
/**
 * Assessment Title: Portfolio Part 2
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * Joke.php - Joke model handles:

*  Joke content (title, body)
* Publication date
* Soft deletion
* Relationships with users (author)
* Many-to-many relationship with categories
 *
 *
 *
 * Filename:        Joke.php
 * Location:        /App/Models
 * Project:         jokes-api-2025-s2
 * Date Created:    15/07/2025
 * Version:         1.0.0
 *
 * Author:          Adrian Gould <Adrian.Gould@nmtafe.wa.edu.au>
 *                  Maria Benic <j317578@tafe.wa.edu.au>
 */ 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Category;
use App\Models\User;


use Illuminate\Database\Eloquent\SoftDeletes;

class Joke extends Model
{
    /** @use HasFactory<\Database\Factories\JokeFactory> */
    use HasFactory;
      //Allows jokes to be "deleted" without being permanently removed from the database. Adds a deleted_at column.
    use SoftDeletes;

    protected $table = 'jokes';

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];

    }

    public function user(): BelongsTo
    {
        //Each joke is submitted by one user
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
        /*
    *Indicates a many-to-many relationship:

    * A joke can have multiple categories.

    * A category can contain many jokes.

    * This uses a pivot table category_joke */
    }
}
