<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisCode extends Model
{
    protected $fillable = [
        'branch_id',
        'code',
        'description',
    ];

    // İlişkiler
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function molecules()
    {
        return $this->belongsToMany(
            Molecule::class,
            'diagnosis_molecules', // tablo ismi migration ile aynı olmalı
            'diagnosis_code_id',   // pivot tablodaki diagnosis_code_id
            'molecule_id'          // pivot tablodaki molecule_id
        );
    }


}
