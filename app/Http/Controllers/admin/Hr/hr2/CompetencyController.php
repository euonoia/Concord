<?php
namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr2\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetencyController extends Controller
{
    /**
     * Check if the user is an HR Admin.
     * We use a private helper to keep the code clean.
     */
    private function authorizeHrAdmin()
    {
        if (Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized action. This area is restricted to HR Administrators.');
        }
    }

    // View the list
    public function index() {
        $this->authorizeHrAdmin();
        
        $competencies = Competency::all(); 
        return view('admin.hr2.competencies', compact('competencies'));
    }

    // Save a new one
    public function store(Request $request)
    {
        $this->authorizeHrAdmin();

        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:competencies_hr2,code',
            'title'            => 'required|string|max:100',
            'competency_group' => 'required|string',
            'description'      => 'nullable|string',
        ]);

     Competency::create($validated);

        return redirect()->back()->with('success', 'New competency added successfully!');
    }

    // Delete one
    public function destroy($id) {
        $this->authorizeHrAdmin();

        Competency::destroy($id);
        return back()->with('info', 'Competency removed.');
    }
}