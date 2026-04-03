<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            $model->type = 'App\\Models\\RegistrationFee';
        });

        static::addGlobalScope('type', function ($query) {
            $query->where('type', 'App\\Models\\RegistrationFee');
        });
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}