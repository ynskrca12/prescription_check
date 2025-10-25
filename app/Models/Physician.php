<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Physician extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'physician_code',
        'name',
        'surname',
        'tc_no',
        'diploma_no',
        'branch_id',
        'password',
        'is_active',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // İlişkiler
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function prescriptionChecks()
    {
        return $this->hasMany(PrescriptionCheck::class, 'user_id');
    }

    // Helper metodlar
    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}
