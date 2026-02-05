<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 'employee_number', 'first_name', 'last_name',
        'department', 'department_id', 'designation', 'join_date', 'base_salary',
        'phone', 'email', 'is_active',
        'employee_code', 'name', 'employment_type', 'salary', 'shift', 'status', 'photo',
        'nid_number', 'nid_photo',
    ];

    protected $casts = [
        'join_date' => 'date',
        'base_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
    ];

    /** Display name: prefer single name field, fallback to first + last (existing logic). */
    public function getDisplayNameAttribute(): string
    {
        if (! empty($this->name)) {
            return trim($this->name);
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFullNameAttribute(): string
    {
        return $this->display_name;
    }

    /** Photo URL for display; returns default avatar path when photo is null. */
    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->photo)) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-avatar.svg');
    }

    /** NID photo URL for display; null when not uploaded. */
    public function getNidPhotoUrlAttribute(): ?string
    {
        if (empty($this->nid_photo)) {
            return null;
        }
        return asset('storage/' . $this->nid_photo);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** NEW – SAFE ADDITION: Optional department (from departments table). */
    public function departmentRelation(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /** @return HasMany<Attendance, $this> */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
