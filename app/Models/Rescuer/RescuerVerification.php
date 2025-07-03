<?php

namespace App\Models\Rescuer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shared\User;

class RescuerVerification extends Model
{
    protected $fillable = [
        'rescuer_id',
        'submitted_by',
        'document_url',
        'facebook_link',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function rescuer(): BelongsTo
    {
        return $this->belongsTo(Rescuer::class, 'rescuer_id', 'rescuer_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by', 'user_id');
    }
}