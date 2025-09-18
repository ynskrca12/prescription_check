<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoleculeLabRule extends Model
{
    protected $fillable = [
        'molecule_id',
        'laboratory_parameter_id',
        'operator',
        'value',
        'explanation',
    ];

    // Kuralın hangi ilaç için olduğunu döner
    public function molecule()
    {
        return $this->belongsTo(Molecule::class);
    }

    // Hangi laboratuvar parametresi için
    public function parameter()
    {
        return $this->belongsTo(LaboratoryParameter::class, 'laboratory_parameter_id');
    }
}
