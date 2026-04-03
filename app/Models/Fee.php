<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'type',
        'total_amount',
        'academic_year',
        'due_before',
        'title',
        'classroom',
        'grade_id',
        'description',
        'number_of_installments',
        'late_fine_per_week',
        'required',
    ];

    protected $casts = [
        'total_amount'       => 'decimal:2',
        'late_fine_per_week' => 'decimal:2',
        'number_of_installments' => 'integer',
        'required'           => 'boolean',
        'due_before'         => 'date',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the installments for this tuition fee.
     */
    public function installments()
    {
        return $this->hasMany(Installment::class, 'tuition_fee_id');
    }
}
