<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrayReportStatusLog extends Model
{
    protected $table = 'stray_report_status_logs';
    protected $primaryKey = 'log_id';
    
    public $timestamps = false; // We're using changed_at instead
    
    protected $fillable = [
        'adopter_id',
        'old_status',
        'new_status',
        'changed_by',
        'changed_at',
        'notes'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function adopter(): BelongsTo
    {
        return $this->belongsTo(Adopter::class, 'adopter_id', 'adopter_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by', 'user_id');
    }
}