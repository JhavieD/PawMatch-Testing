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
    ];

    public function adopter()
    {
        return $this->belongsTo(\App\Models\Adopter\Adopter::class, 'adopter_id', 'adopter_id');
    }
}