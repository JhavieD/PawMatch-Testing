<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionApplication extends Model
{
    use HasFactory;

    protected $table = 'adoption_applications';

    protected $primaryKey = 'application_id';
    protected $fillable = [
        'adopter_id', 'pet_id', 'shelter_id', 'rescuer_id', 'reason_for_adoption', 'living_environment', 'experience_with_pets', 'household_members', 'allergies', 'has_other_pets', 'other_pets_details', 'can_provide_vet_care', 'status', 'rejection_reason', 'submitted_at', 'reviewed_at'
    ];

    public function adopter() { return $this->belongsTo(\App\Models\Adopter::class, 'adopter_id', 'adopter_id'); }
    public function pet() { return $this->belongsTo(Pet::class, 'pet_id'); }
    public function shelter() { return $this->belongsTo(Shelter::class, 'shelter_id'); }
    public function rescuer() { return $this->belongsTo(Rescuer::class, 'rescuer_id'); }
    public function answers() { return $this->hasMany(ApplicationAnswer::class, 'application_id'); }
    
} 