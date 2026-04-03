<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone_number',
        'password',
        'verified',
        'email_verified_at',
        // Student-specific fields
        'matricule',
        'classroom',
        'academic_year',
        'parent1_name',
        'parent1_surname',
        'parent1_phone',
        'parent2_name',
        'parent2_surname',
        'parent2_phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified' => 'boolean',
        ];
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin panel - only for admin role
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }

        // Portal panel - only for parent_student role
        if ($panel->getId() === 'portal') {
            return $this->hasRole('parent_student');
        }

        // Staff panel - for accountant, employee, secretary
        if ($panel->getId() === 'staff') {
            return $this->hasAnyRole(['accountant', 'employee', 'secretary']);
        }

        return false;
    }

    /**
     * Check if user is a student (has parent_student role)
     */
    public function isStudent(): bool
    {
        return $this->hasRole('parent_student');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is staff member
     */
    public function isStaff(): bool
    {
        return $this->hasAnyRole(['accountant', 'employee', 'secretary']);
    }

    public function getFilamentName(): string
    {
        return "{$this->name} {$this->surname}";
    }

    /** * Override parent to make it specific to TuitionFee.
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->surname}";
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
