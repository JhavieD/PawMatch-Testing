<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdopterReview extends Model
{
    protected $table = 'adopter_reviews';
    protected $primaryKey = 'review_id';
    public $timestamps = false;

    // Relationships
    public function shelter()
    {
        return $this->belongsTo(\App\Models\Shelter::class, 'shelter_id', 'shelter_id');
    }

    public function adopter()
    {
        return $this->belongsTo(\App\Models\Adopter::class, 'adopter_id', 'adopter_id');
    }

    public function rescuer()
    {
        return $this->belongsTo(\App\Models\Rescuer::class, 'rescuer_id', 'rescuer_id');
    }
}
