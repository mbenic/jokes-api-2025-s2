<?php
/**
 * Assessment Title: Portfolio Part 2
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * User.php the User model- Represents a User, defines the structure, relationships, and behavior of users in the system
 * This model handles:  
 *  User authentication (email, password)
 *  *User profile (name, email)
 *  User roles (admin, regular user)
 *  User relationships (jokes authored)
 *  *User soft deletion
 *  *User verification (email verification)
 *  User password hashing
 *
 *
 *
 * Filename:        User.php
 * Location:        /App/Models
 * Project:         jokes-api-2025-s2
 * Date Created:    15/07/2025
 * Version:         1.0.0
 *
 * Author:          Adrian Gould <Adrian.Gould@nmtafe.wa.edu.au>
 *                  Maria Benic <j317578@tafe.wa.edu.au>
 */ 

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Joke;

use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jokes(): HasMany
    {   //A user can create many jokes.
        return $this->hasMany(Joke::class);
    }
}
