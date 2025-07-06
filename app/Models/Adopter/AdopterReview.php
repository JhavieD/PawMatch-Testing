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
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'shelter_id',
        'rescuer_id', 
        'adopter_id',
        'rating',
        'review',
        'created_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime'
    ];

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
    
    public function getRelatedApplication()
    {
        if ($this->shelter_id) {
            return AdoptionApplication::where('adopter_id', $this->adopter_id)
                ->where('shelter_id', $this->shelter_id)
                ->where('status', 'completed')
                ->with('pet')
                ->first();
        } elseif ($this->rescuer_id) {
            return AdoptionApplication::where('adopter_id', $this->adopter_id)
                ->where('rescuer_id', $this->rescuer_id)
                ->where('status', 'completed')
                ->with('pet')
                ->first();
        }
        return null;
    }
}
