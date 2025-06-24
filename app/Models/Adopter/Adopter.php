<?php

namespace App\Models\Adopter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shared\User;
use App\Models\Shared\Pet;
use App\Models\Shared\AdoptionApplication;
use App\Models\Adopter\AdopterReview;

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
        return $this->belongsTo(\App\Models\Shared\User::class, 'user_id', 'user_id');
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
        return $this->hasMany(\App\Models\Adopter\AdopterReview::class, 'adopter_id', 'adopter_id');
    }
} 