<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Fee;

class Grade extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description'];

    public function registrationFees(): HasMany
    {
        return $this->hasMany(Fee::class, 'grade_id')
                    ->where('type', 'App\\Models\\RegistrationFee');
    }

    public function tuitionFees(): HasMany
    {
        return $this->hasMany(Fee::class, 'grade_id')
                    ->where('type', 'App\\Models\\TuitionFee');
    }

    public function classRegistrations(): HasMany
    {
        return $this->hasMany(ClassRegistration::class);
    }
}