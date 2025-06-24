<?php

namespace App\Models\Adopter;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shelter\Shelter;
use App\Models\Adopter\Adopter;
use App\Models\Rescuer\Rescuer;

class AdopterReview extends Model
{
    protected $table = 'adopter_reviews';
    protected $primaryKey = 'review_id';
    public $timestamps = false;

    // Relationships
    public function shelter()
    {
        return $this->belongsTo(Shelter::class, 'shelter_id', 'shelter_id');
    }

    public function adopter()
    {
        return $this->belongsTo(Adopter::class, 'adopter_id', 'adopter_id');
    }

    public function rescuer()
    {
        return $this->belongsTo(Rescuer::class, 'rescuer_id', 'rescuer_id');
    }
}
