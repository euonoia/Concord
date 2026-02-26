<?php

namespace App\Http\Controllers\core1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\core1\Appointment;

class DischargeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Appointment::with(['patient', 'doctor'])
            ->where('status', 'completed');

        // Doctor sees only their patients
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        }

        $appointments = $query->latest('appointment_date')->paginate(10);

        return view('core.core1.discharge.index', compact('appointments'));
    }
}
