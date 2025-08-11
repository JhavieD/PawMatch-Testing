<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shared\User;

class Donation extends Model
{
    use HasFactory;

    protected $primaryKey = 'donation_id';

    protected $fillable = [
        'user_id',
        'donor_name',
        'donor_email',
        'amount',
        'message',
        'maya_payment_id',
        'payment_status',
        'payment_date',
        'maya_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'maya_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
} 