<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use App\Models\Adopter\Adopter;

class StrayReports extends Model
{
    protected $table = 'stray_reports'; 
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'adopter_id',
        'location',
        'description',
        'status',
        'reported_at',
        'image_url',      
        'animal_type',
        'is_flagged',
        'flag_reason',
        'is_duplicate',
        'flagged_by',
        'flagged_at',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
        'is_duplicate' => 'boolean',
        'flagged_at' => 'datetime',
    ];

    public function adopter()
    {
        return $this->belongsTo(\App\Models\Adopter\Adopter::class, 'adopter_id', 'adopter_id');
    }
}