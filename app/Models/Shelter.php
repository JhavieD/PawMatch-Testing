<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pet;
use App\Models\AdopterReview;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pets()
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
}
