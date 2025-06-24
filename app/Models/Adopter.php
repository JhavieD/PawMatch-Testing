<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adopter extends Model
{
    use HasFactory;

    protected $primaryKey = 'adopter_id';

    protected $fillable = [
        'user_id',
        'address',
        'adoption_status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }

    public function savedPets()
    {
        return $this->belongsToMany(Pet::class, 'saved_pets', 'adopter_id', 'pet_id')->withTimestamps();
    }

    public function applications()
    {
        return $this->hasMany(AdoptionApplication::class, 'adopter_id', 'adopter_id');
    }

    public function adopterReviews() {
        return $this->hasMany(\App\Models\AdopterReview::class, 'adopter_id', 'adopter_id');
    }
} 