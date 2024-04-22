<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class User extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'image_path',
    ];

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }
     
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
     
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function participants(): MorphToMany
    {
        return $this->morphToMany(Program::class, 'entity', 'program_participants');
    }
}
