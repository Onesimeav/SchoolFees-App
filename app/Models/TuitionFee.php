<?php

namespace App\Models;

class TuitionFee extends Fee
{
    protected $table = 'fees';

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->type = 'App\\Models\\TuitionFee';
        });

        static::addGlobalScope('type', function ($query) {
            $query->where('type', 'App\\Models\\TuitionFee');
        });
    }

    /**
     * Get the installments for this tuition fee.
     */
    public function installments()
    {
        return $this->hasMany(Installment::class, 'tuition_fee_id');
    }
}