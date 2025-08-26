<?php
/**
 * Assessment Title: Portfolio Project
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * Category.php the Category model- Represents a category 
 *
 *
 * Filename:        Category.php
 * Location:        /App/Models
 * Project:         jokes-api-2025-s2
 * Date Created:    15/07/2025
 *
 * Author:          Adrian Gould <Adrian.Gould@nmtafe.wa.edu.au>
 *                  Maria Benic <j317578@tafe.wa.edu.au>
 */ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Joke;
 

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'description',
    ];

   // public static function create(array $seedCategory) {}

    public function jokes():BelongsToMany
    {

        return $this->belongsToMany(Joke::class);
    }

    /** return the collection of related jokes in reverse order of their title */

    public function jokesByTitleDesc(): BelongsToMany
    {
        return $this->jokes()->orderBy('title', 'desc');
    }

    public function jokesByTitle(): BelongsToMany
    {
        return $this->jokes()->orderBy('title');
    }

    /**
     * rerurns the collection of related jokes in reverse order by their creation date
     */

    public function jokesByDateAddedDesc(): BelongsToMany
    {
        return $this->jokes()->orderBy('created_at', 'desc');
    }
}
