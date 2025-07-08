<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shelter\Shelter;
use App\Models\Shared\PetImage;

class Pet extends Model
{
    use HasFactory;

    protected $primaryKey = 'pet_id';

    protected $fillable = [
        'shelter_id',
        'rescuer_id',
        'name',
        'species',
        'breed',
        'age',
        'gender',
        'size',
        'medical_history',
        'adoption_status',
        'behavior',
        'daily_activity',
        'eating_habits',
        'special_needs',
        'compatibility',
        'suitable_for',
        'description'
    ];

    protected $casts = [
        'medical_history' => 'array',
    ];

    // Relationships can be added here as needed
    public function shelter()
    {
        return $this->belongsTo(Shelter::class, 'shelter_id');
    }
    public function rescuer()
    {
        return $this->belongsTo(\App\Models\Rescuer\Rescuer::class, 'rescuer_id');
    }
    //Pet Image Upload
    public function images()
    {
        return $this->hasMany(PetImage::class, 'pet_id', 'pet_id');
    }
    public function getImageUrlAttribute()
    {
        return $this->images->first()->image_url ?? null;
    }
}
