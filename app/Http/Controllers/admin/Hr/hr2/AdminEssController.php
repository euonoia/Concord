<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\EssRequest; 
use Illuminate\Support\Facades\Auth;

class AdminEssController extends Controller
{
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();

        $requests = EssRequest::with('employee')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.hr2.ess', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'status' => 'required|in:approved,rejected,closed',
        ]);

        $ess = EssRequest::findOrFail($id);
        $ess->status = $request->status;
        $ess->save();
        if (in_array($request->status, ['approved', 'rejected'])) {
            if (method_exists($ess, 'archive')) {
                $ess->archive();
            }
        }

        return redirect()->back()->with('success', 'Request status updated successfully.');
    }
}