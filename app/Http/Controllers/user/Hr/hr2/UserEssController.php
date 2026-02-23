<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\EssRequest;
use App\Models\user\Hr\hr2\EssRequestArchive;
use App\Models\Employee; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEssController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $active = EssRequest::where('employee_id', $employee->employee_id)->get();
        $archived = EssRequestArchive::where('employee_id', $employee->employee_id)->get();

        $requests = $active->concat($archived)->sortByDesc('created_at');

        return view('hr.hr2.ess', compact('requests', 'employee'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

      
        $employee = Employee::where('user_id', Auth::id())->first();

        $request->validate([
            'type' => 'required|string|max:255',
            'details' => 'required|string|max:2000',
        ]);

    
        $lastEss = EssRequest::orderBy('created_at', 'desc')->first();
        $lastNumber = $lastEss ? (int) preg_replace('/\D/', '', $lastEss->ess_id) : 0;
        $ess_id = 'ESS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        EssRequest::create([
            'ess_id' => $ess_id,
            'employee_id' => $employee->employee_id, 
            'type' => $request->type,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->route('user.ess.index')->with('success', 'Request ' . $ess_id . ' has been submitted.');
    }
}