<?php

namespace App\Models\Rescuer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shared\User;
use App\Models\Shared\Pet;
use App\Models\Shared\AdoptionApplication;
use App\Models\Shared\Message;
use App\Models\Adopter\AdopterReview; // If you have reviews for rescuers
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Rescuer extends Model
{
    use HasFactory;

    protected $primaryKey = 'rescuer_id';

    protected $fillable = [
        'user_id',
        'organization_name',
        'location',
        'verified',
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pets()
    {
        return $this->hasMany(Pet::class, 'rescuer_id', 'rescuer_id');
    }

    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(
            AdoptionApplication::class,
            Pet::class,
            'rescuer_id',
            'pet_id',
            'rescuer_id',
            'pet_id'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AdopterReview::class, 'rescuer_id', 'rescuer_id');
    }
    public function verifications()
    {
        return $this->hasMany(RescuerVerification::class, 'rescuer_id', 'rescuer_id');
    }
}
