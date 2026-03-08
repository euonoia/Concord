<?php
namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\EssRequest; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 

class AdminEssController extends Controller
{
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();
        $requests = EssRequest::with('employee')->orderBy('created_at', 'desc')->get();
        return view('admin.hr2.ess', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
{
    $this->authorizeHrAdmin();

    $request->validate(['status' => 'required|in:approved,rejected,closed']);

    return DB::transaction(function () use ($request, $id) {
        $ess = EssRequest::findOrFail($id);
        
        // 1. Force the status update first
        $ess->update(['status' => $request->status]);
        Log::info("Step 1: Request #{$id} status set to {$request->status}");

        // 2. Deactivate Shift (Using direct DB update to bypass all Model logic)
        // We use trim() and strtolower() to prevent "ignore" bugs
        if ($request->status === 'approved' && strtolower(trim($ess->type)) === 'leave') {
            
            $shiftId = (int)$ess->shift_id;
            
            if ($shiftId > 0) {
                $affected = DB::table('shifts_hr3')
                    ->where('id', $shiftId)
                    ->update(['is_active' => 0]);
                
                Log::info("Step 2: Shift #{$shiftId} update attempted. Rows changed: {$affected}");
            }
        }

        // 3. Archive (Wrapped so it CANNOT stop Step 1 and 2)
        if (in_array($request->status, ['approved', 'rejected'])) {
            try {
                // We reload the data to make sure archive has the latest status
                $ess->refresh(); 
                if (method_exists($ess, 'archive')) {
                    $ess->archive();
                    Log::info("Step 3: Archived successfully.");
                }
            } catch (\Exception $e) {
                Log::error("Step 3 FAILED: " . $e->getMessage());
               
            }
        }

        return redirect()->back()->with('success', 'Processed.');
    });
}
}