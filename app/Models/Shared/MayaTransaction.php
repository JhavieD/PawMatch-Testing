<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Adopter\Adopter;
use App\Models\Shelter\Shelter;
use App\Models\Rescuer\Rescuer;

class MayaTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'application_id',
        'adopter_id',
        'shelter_id',
        'rescuer_id',
        'total_amount',
        'pawmatch_commission',
        'provider_amount',
        'maya_payment_id',
        'payment_status',
        'payment_method',
        'payment_date',
        'payout_status',
        'payout_date',
        'payout_reference',
        'maya_response',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'pawmatch_commission' => 'decimal:2',
        'provider_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'payout_date' => 'datetime',
        'maya_response' => 'array'
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(AdoptionApplication::class, 'application_id', 'application_id');
    }

    public function adopter()
    {
        return $this->belongsTo(Adopter::class, 'adopter_id', 'adopter_id');
    }

    public function shelter()
    {
        return $this->belongsTo(Shelter::class, 'shelter_id', 'shelter_id');
    }

    public function rescuer()
    {
        return $this->belongsTo(Rescuer::class, 'rescuer_id', 'rescuer_id');
    }

    // Helper methods
    public function getProviderNameAttribute()
    {
        if ($this->shelter) {
            return $this->shelter->shelter_name;
        }
        if ($this->rescuer) {
            return $this->rescuer->organization_name;
        }
        return 'Unknown Provider';
    }

    public function getProviderTypeAttribute()
    {
        if ($this->shelter) {
            return 'shelter';
        }
        if ($this->rescuer) {
            return 'rescuer';
        }
        return null;
    }

    // Calculate commission (20% of total amount)
    public static function calculateCommission($totalAmount)
    {
        return $totalAmount * 0.20;
    }

    // Calculate provider amount (80% of total amount)
    public static function calculateProviderAmount($totalAmount)
    {
        return $totalAmount * 0.80;
    }
} 