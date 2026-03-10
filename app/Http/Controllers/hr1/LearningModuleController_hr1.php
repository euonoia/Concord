<?php

namespace App\Http\Controllers\hr1;

use App\Http\Controllers\Controller;
use App\Models\hr1\LearningModule_hr1;
use App\Models\hr1\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningModuleController_hr1 extends Controller
{
    public function index()
    {
        $modules = LearningModule_hr1::all();
        return response()->json($modules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $module = LearningModule_hr1::create($validated);
        return response()->json($module, 201);
    }

    public function assign(Request $request, $userId)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:learning_modules_hr1,id',
        ]);

        $user = User::findOrFail($userId);
        $user->learningModules_hr1()->syncWithoutDetaching([$validated['module_id']]);

        return response()->json(['message' => 'Module assigned successfully']);
    }

    public function markComplete($id)
    {
        $assignment = \DB::table('user_learning_modules_hr1')->where('id', $id)->first();
        
        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }

        \DB::table('user_learning_modules_hr1')
            ->where('id', $id)
            ->update(['completed' => 1, 'updated_at' => now()]);

        return response()->json(['message' => 'Module marked as completed']);
    }
}

