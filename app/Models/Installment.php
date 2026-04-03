<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::creating(function (Installment $model): void {
            if (is_null($model->number)) {
                $model->number = 0; // Overwritten by 'created' renumber hook
            }
        });

        $renumber = static function (Installment $installment): void {
            static::where('tuition_fee_id', $installment->tuition_fee_id)
                ->orderBy('due_date')
                ->get()
                ->each(function (Installment $inst, int $index): void {
                    $inst->updateQuietly(['number' => $index + 1]);
                });
        };

        static::created($renumber);

        static::updated(function (Installment $installment) use ($renumber): void {
            if ($installment->wasChanged('due_date')) {
                $renumber($installment);
            }
        });

        static::deleted($renumber);
    }

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
