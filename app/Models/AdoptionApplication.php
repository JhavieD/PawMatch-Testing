<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionApplication extends Model
{
    use HasFactory;

    protected $primaryKey = 'application_id';
    protected $fillable = [
        'adopter_id', 'pet_id', 'shelter_id', 'rescuer_id', 'reason_for_adoption', 'living_environment',
        'experience_with_pets', 'household_members', 'allergies', 'has_other_pets', 'other_pets_details',
        'can_provide_vet_care', 'status', 'rejection_reason', 'submitted_at'
    ];
} 