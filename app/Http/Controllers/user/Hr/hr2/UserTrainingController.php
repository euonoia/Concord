<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\TrainingSession;
use App\Models\user\Hr\hr2\TrainingEnroll;
use App\Models\Employee; 
use Illuminate\Support\Facades\Auth;
use Exception; 

class UserTrainingController extends Controller
{
    public function index()
    {
        if (!Auth::check()) return redirect()->route('portal.login');

        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

      
        $sessions = TrainingSession::with(['enrolls' => function ($q) use ($employeeRecord) {
                $q->where('employee_id', $employeeRecord->employee_id);
            }])
            ->orderBy('start_datetime', 'asc')
            ->get();

        return view('hr.hr2.training', compact('sessions'));
    }

   public function enroll($id)
{
    try {
        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
             return "Error: Employee record not found for user " . Auth::id();
        }

       
        $enroll = new TrainingEnroll();
        $enroll->employee_id = (string) $employeeRecord->employee_id;
        $enroll->training_id = (string) $id;
        $enroll->status = 'enrolled';
        
       
        $enroll->save(); 

        return redirect('/hr/my-training')->with('success', 'Saved to database!');

    } catch (\Exception $e) {
    
        dd("Database Error: " . $e->getMessage());
    }
}
}