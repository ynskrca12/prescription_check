<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Molecule extends Model
{
    protected $fillable = [
        'name',
        'generic_name',
    ];

    // Bir ilacın birden fazla tanısı olabilir (many-to-many)
    public function diagnoses()
    {
        return $this->belongsToMany(DiagnosisCode::class, 'diagnosis_molecule');
    }

    // Bir ilacın birden fazla laboratuvar kuralı olabilir (one-to-many)
    public function labRules()
    {
        return $this->hasMany(MoleculeLabRule::class);
    }
}
