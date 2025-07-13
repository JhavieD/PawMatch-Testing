<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pet\Pet;
use App\Models\Adopter\Adopter;

class AdoptionApplication extends Model
{
    protected $table = 'adoption_applications';
    protected $primaryKey = 'application_id';
    
    protected $fillable = [
        'adopter_id',
        'pet_id',
        'application_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'application_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function adopter()
    {
        return $this->belongsTo(Adopter::class, 'adopter_id', 'adopter_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'pet_id');
    }
}