<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $primaryKey = 'pet_id';

    protected $fillable = [
        'shelter_id', 'rescuer_id', 'name', 'species', 'breed', 'age', 'gender', 'size', 'medical_history', 'adoption_status', 'behavior', 'daily_activity', 'eating_habits', 'special_needs', 'compatibility', 'description'
    ];

    // Relationships can be added here as needed
    public function shelter()
    {
        return $this->belongsTo(\App\Models\Shelter::class, 'shelter_id');
    }
    //Pet Image Upload
    public function images()
    {
    return $this->hasMany(\App\Models\PetImage::class, 'pet_id', 'pet_id');
    }
}   