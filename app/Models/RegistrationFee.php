<?php

namespace App\Models;

class RegistrationFee extends Fee
{
    protected $table = 'fees';

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->type = 'RegistrationFee';
        });

        static::addGlobalScope('type', function ($query) {
            $query->where('type', 'RegistrationFee');
        });
    }
}