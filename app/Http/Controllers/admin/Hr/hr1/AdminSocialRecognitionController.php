<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use App\Models\RecognitionPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminSocialRecognitionController extends Controller
{
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHr1Admin();
        $posts = RecognitionPost::with(['admin', 'employee'])->orderBy('created_at', 'desc')->get();
        
        // Metrics
        $totalPosts = $posts->count();
        $totalLikes = $posts->sum('likes_count');
        $totalComments = $posts->sum('comments_count');
        $engagementRate = $totalPosts > 0 ? round(($totalLikes + $totalComments) / $totalPosts, 1) : 0;

        return view('admin.hr1.social recognition.index', compact('posts', 'totalPosts', 'totalLikes', 'totalComments', 'engagementRate'));
    }

    public function create()
    {
        $this->authorizeHr1Admin();
        $employees = \App\Models\Employee::join('users', 'employees.user_id', '=', 'users.id')
            ->join('new_hires_hr1', 'users.email', '=', 'new_hires_hr1.email')
            ->where('new_hires_hr1.status', 'active')
            ->select('employees.*')
            ->orderBy('employees.last_name')
            ->get();
            
        return view('admin.hr1.social recognition.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->authorizeHr1Admin();

        $request->validate([
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,employee_id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recognition', 'public');
        }

        $post = RecognitionPost::create([
            'admin_id' => Auth::id(),
            'employee_id' => $request->input('employee_id'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image_path' => $imagePath,
        ]);

        if ($request->has('award_bonus')) {
            return $this->syncToHr4($post->id);
        }

        return redirect()->route('admin.hr1.recognition.index')->with('success', 'Recognition post created successfully.');
    }

    public function edit($id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::findOrFail($id);
        $employees = \App\Models\Employee::join('users', 'employees.user_id', '=', 'users.id')
            ->join('new_hires_hr1', 'users.email', '=', 'new_hires_hr1.email')
            ->where('new_hires_hr1.status', 'active')
            ->select('employees.*')
            ->orderBy('employees.last_name')
            ->get();
            
        return view('admin.hr1.social recognition.edit', compact('post', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,employee_id',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $post->image_path = $request->file('image')->store('recognition', 'public');
        }

        $post->update([
            'title' => $request->input('title'),
            'employee_id' => $request->input('employee_id'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.hr1.recognition.index')->with('success', 'Recognition post updated successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::findOrFail($id);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()->route('admin.hr1.recognition.index')->with('success', 'Recognition post deleted successfully.');
    }

    /**
     * Sync recognition to HR4 Compensation (Award Bonus)
     */
    public function syncToHr4($id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::with('employee')->findOrFail($id);

        if (!$post->employee_id) {
            return redirect()->back()->with('error', 'No employee linked to this recognition.');
        }

        $month = date('Y-m');
        $bonusAmount = 500.00; // Default bonus for recognition

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Update Direct Compensation (Bonus column)
            // Note: We check if a record exists for this month, if not we create it (similar to NewHireController logic)
            $directComp = \App\Models\admin\Hr\hr4\DirectCompensation::firstOrCreate(
                ['employee_id' => $post->employee_id, 'month' => $month],
                ['base_salary' => 0, 'shift_allowance' => 0, 'bonus' => 0]
            );
            $directComp->increment('bonus', $bonusAmount);

            // 2. Add to Indirect Compensation as requested
            \App\Models\admin\Hr\hr4\IndirectCompensation::create([
                'employee_id' => $post->employee_id,
                'month' => $month,
                'benefit_name' => 'Social Recognition: ' . $post->title,
                'amount' => $bonusAmount,
                'description' => 'Bonus awarded via Social Recognition module.',
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.hr1.recognition.index')->with('success', "Bonus of {$bonusAmount} successfully awarded to {$post->employee->full_name} in HR4 Compensation Planning.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to award bonus: ' . $e->getMessage());
        }
    }
}
