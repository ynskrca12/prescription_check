<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PrescriptionService;

class PrescriptionController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }//End
}
