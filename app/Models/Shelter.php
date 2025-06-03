<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
} 