<?php

namespace App\Http\Controllers\hr1;

use App\Http\Controllers\Controller;
use App\Models\hr1\Recognition_hr1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecognitionController_hr1 extends Controller
{
    public function index()
    {
        $recognitions = Recognition_hr1::latest()->get();
        return response()->json($recognitions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|string|max:255',
            'reason' => 'required|string',
            'award_type' => 'required|string|max:255',
        ]);

        $recognition = Recognition_hr1::create([
            'from' => Auth::user()->name ?? 'System',
            'to' => $validated['to'],
            'reason' => $validated['reason'],
            'award_type' => $validated['award_type'],
            'date' => now(),
            'congratulations' => 0,
            'boosts' => 0,
        ]);

        return response()->json($recognition, 201);
    }

    public function congratulate($id)
    {
        $recognition = Recognition_hr1::findOrFail($id);
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        $action = \DB::table('recognition_user_actions_hr1')
            ->where('user_id', $userId)
            ->where('recognition_id', $id)
            ->first();
        if ($action && $action->congratulated) {
            return response()->json($recognition); // Already congratulated, return current state
        }
        if (!$action) {
            \DB::table('recognition_user_actions_hr1')->insert([
                'user_id' => $userId,
                'recognition_id' => $id,
                'congratulated' => true,
                'boosted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            \DB::table('recognition_user_actions_hr1')
                ->where('user_id', $userId)
                ->where('recognition_id', $id)
                ->update(['congratulated' => true, 'updated_at' => now()]);
        }
        $recognition->increment('congratulations');
        return response()->json($recognition->fresh());
    }

    public function boost($id)
    {
        $recognition = Recognition_hr1::findOrFail($id);
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        $action = \DB::table('recognition_user_actions_hr1')
            ->where('user_id', $userId)
            ->where('recognition_id', $id)
            ->first();
        if ($action && $action->boosted) {
            return response()->json($recognition); // Already boosted, return current state
        }
        if (!$action) {
            \DB::table('recognition_user_actions_hr1')->insert([
                'user_id' => $userId,
                'recognition_id' => $id,
                'congratulated' => false,
                'boosted' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            \DB::table('recognition_user_actions_hr1')
                ->where('user_id', $userId)
                ->where('recognition_id', $id)
                ->update(['boosted' => true, 'updated_at' => now()]);
        }
        $recognition->increment('boosts');
        return response()->json($recognition->fresh());
    }

    public function update(Request $request, $id)
    {
        $recognition = Recognition_hr1::findOrFail($id);
        
        $validated = $request->validate([
            'to' => 'sometimes|string|max:255',
            'from' => 'sometimes|string|max:255',
            'reason' => 'sometimes|string',
            'award_type' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
        ]);

        $recognition->update($validated);
        return response()->json($recognition);
    }

    public function destroy($id)
    {
        $recognition = Recognition_hr1::findOrFail($id);
        $recognition->delete();
        return response()->json(['message' => 'Recognition deleted successfully']);
    }
}

