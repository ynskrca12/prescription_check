<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaboratoryParameter extends Model
{
    protected $fillable = [
        'name',
        'unit',
    ];

    // Laboratuvar parametresi birden fazla ilaç kuralında kullanılabilir
    public function moleculeRules()
    {
        return $this->hasMany(MoleculeLabRule::class, 'laboratory_parameter_id');
    }
}
