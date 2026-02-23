<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\EssRequest;
use App\Models\user\Hr\hr2\EssRequestArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEssController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        $employee = Auth::user();

        // Fetch from active table
        $active = EssRequest::where('employee_id', $employee->id)->get();

        // Fetch from archive table
        $archived = EssRequestArchive::where('employee_id', $employee->id)->get();

        // Combine and sort by date descending
        $requests = $active->concat($archived)->sortByDesc('created_at');

        return view('hr.hr2.ess', compact('requests'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user();

        $request->validate([
            'type' => 'required|string|max:255',
            'details' => 'required|string|max:2000',
        ]);

        // Generate unique ESS ID (e.g., ESS0001)
        $lastEss = EssRequest::orderBy('id', 'desc')->first();
        $lastNumber = $lastEss ? (int) preg_replace('/\D/', '', $lastEss->ess_id) : 0;
        $ess_id = 'ESS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        EssRequest::create([
            'ess_id' => $ess_id,
            'employee_id' => $employee->id,
            'type' => $request->type,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->route('user.ess.index')->with('success', 'Request ' . $ess_id . ' has been submitted.');
    }
}