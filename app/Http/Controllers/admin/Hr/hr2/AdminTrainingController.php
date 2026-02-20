<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\TrainingSessions; 
use Illuminate\Support\Facades\Auth;

class AdminTrainingController extends Controller
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

        $sessions = TrainingSessions::withCount('enrolls')
            ->orderBy('start_datetime', 'desc')
            ->get();

        return view('admin.hr2.training', compact('sessions'));
    }

    public function store(Request $request)
    {
        $this->authorizeHrAdmin();

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'nullable|date|after_or_equal:start_datetime',
            'location'       => 'nullable|string|max:255',
            'trainer'        => 'nullable|string|max:255',
            'capacity'       => 'nullable|integer|min:1',
        ]);

        // Logic for unique TRN ID
        $last = TrainingSessions::orderBy('id', 'desc')->first();
        $num = $last ? (int)filter_var($last->training_id, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        $training_id = 'TRN-' . str_pad($num, 4, '0', STR_PAD_LEFT);

        TrainingSessions::create(array_merge($validated, [
            'training_id' => $training_id
        ]));

        return redirect()->back()->with('success', 'Training session added successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeHrAdmin();

        $session = TrainingSessions::findOrFail($id);
        $session->delete();

        return redirect()->back()->with('success', 'Training session archived.');
    }
}