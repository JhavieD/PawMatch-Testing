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
        'image_url' => 'json',
    ];

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StrayReportStatusLog::class, 'adopter_id', 'report_id') 
                    ->orderBy('changed_at', 'desc');
    }

    // Add a method to log status changes
    public function logStatusChange($oldStatus, $newStatus, $changedBy, $notes = null)
    {
        return StrayReportStatusLog::create([
            'adopter_id' => $this->report_id, 
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'changed_at' => now(),
            'notes' => $notes
        ]);
    }

    public function adopter()
    {
        return $this->belongsTo(\App\Models\Adopter\Adopter::class, 'adopter_id', 'adopter_id');
    }
}