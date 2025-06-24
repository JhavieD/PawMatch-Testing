<?php

namespace App\Models\Shelter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shared\Pet;
use App\Models\Adopter\AdopterReview;
use App\Models\Shared\AdoptionApplication;
use App\Models\Shared\Message;
use App\Models\Shelter\ShelterVerification;
use App\Models\Shared\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shelter extends Model
{
    use HasFactory;

    protected $primaryKey = 'shelter_id';

    protected $fillable = [
        'user_id',
        'shelter_name',
        'location',
        'contact_info',
        'verified',
        'verified_by',
        'verified_at',
        'avg_adopter_rating',
        'email_notifications',
        'application_updates',
        'marketing_communications'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'email_notifications' => 'boolean',
        'application_updates' => 'boolean',
        'marketing_communications' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'shelter_id', 'shelter_id');
    }

    public function applications()
    {
        return $this->hasMany(AdoptionApplication::class, 'shelter_id', 'shelter_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function adopterReviews()
    {
        return $this->hasMany(AdopterReview::class, 'shelter_id', 'shelter_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ShelterVerification::class, 'shelter_id', 'shelter_id');
    }
}
