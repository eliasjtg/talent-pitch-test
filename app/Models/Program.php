<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'entity', 'program_participants');
    }

    public function challenges(): MorphToMany
    {
        return $this->morphedByMany(Challenge::class, 'entity', 'program_participants');
    }

    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'entity', 'program_participants');
    }
}