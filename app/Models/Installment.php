<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tuition_fee_id',
        'number',
        'amount',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'number' => 'integer',
    ];

    /**
     * Get the tuition fee that owns this installment.
     */
    public function tuitionFee(): BelongsTo
    {
        return $this->belongsTo(Fee::class, 'tuition_fee_id');
    }
}
