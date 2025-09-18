<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function diagnosisCodes()
    {
        return $this->hasMany(DiagnosisCode::class);
    }
}
