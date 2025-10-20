<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionCheck extends Model
{
    protected $fillable = [
        'user_id',
        'molecule_id',
        'answers',
        'lab_values',
        'is_eligible',
        'result_message'
    ];

    protected $casts = [
        'answers' => 'array',
        'lab_values' => 'array',
        'is_eligible' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function molecule()
    {
        return $this->belongsTo(Molecule::class);
    }
}
