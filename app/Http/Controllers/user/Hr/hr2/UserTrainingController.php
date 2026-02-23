<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\TrainingSession;
use App\Models\user\Hr\hr2\TrainingEnroll;
use Illuminate\Support\Facades\Auth;

class UserTrainingController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        $employee = Auth::user();

        // Eager load enrollment status for the current user
        $sessions = TrainingSession::with(['enrolls' => function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            }])
            ->orderBy('start_datetime', 'asc')
            ->get();

        return view('hr.hr2.training', compact('sessions'));
    }

    public function enroll($id)
    {
        $employee = Auth::user();
        $session = TrainingSession::findOrFail($id);

        // Check if training is in the past
        if ($session->start_datetime < now()) {
            return redirect()->back()->with('error', 'This training session has already started or ended.');
        }

        TrainingEnroll::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'training_id' => $session->id,
            ],
            [
                'status' => 'enrolled',
            ]
        );

        return redirect()
            ->route('user.training.index')
            ->with('success', 'Successfully enrolled in ' . $session->title);
    }
}