<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function adopter()
    {
        return $this->belongsTo(\App\Models\Adopter::class, 'adopter_id', 'adopter_id');
    }
}