<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'fee_id',
        'installment_id',
        'amount',
        'date',
        'status',
        'kkiapay_reference',
        'phone_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the fee that this transaction is for.
     */
    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get the installment that this transaction is for.
     */
    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    /**
     * Get the class registration this transaction was created for.
     */
    public function classRegistration()
    {
        return $this->hasOne(ClassRegistration::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }
}
