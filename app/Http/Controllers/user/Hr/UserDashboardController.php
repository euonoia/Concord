<?php

namespace App\Http\Controllers\user\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
     
        $employee = Employee::where('user_id', Auth::id())->first();

  
        if (!$employee) {
           
            $employee = new Employee();
        }

       
        return view('hr.dashboard', compact('employee'));
    }
}