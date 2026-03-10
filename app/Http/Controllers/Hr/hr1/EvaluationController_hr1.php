<?php

namespace App\Http\Controllers\Hr\hr1;

use App\Http\Controllers\Controller;
use App\Models\Hr\hr1\EvaluationCriterion_hr1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController_hr1 extends Controller
{
    private function decodeQuestionsPayload($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeQuestionOptions($options): ?string
    {
        if ($options === null || $options === '') {
            return null;
        }

        if (is_string($options)) {
            $decoded = json_decode($options, true);
            if (is_array($decoded)) {
                $options = $decoded;
            }
        }

        if (!is_array($options)) {
            return null;
        }

        $clean = array_values(array_filter(array_map(function ($v) {
            return is_string($v) ? trim($v) : '';
        }, $options), fn ($v) => $v !== ''));

        return $clean ? json_encode($clean) : null;
    }

    public function index()
    {
        $criteria = EvaluationCriterion_hr1::all();
        return response()->json($criteria);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'section' => 'required|in:A,B,C',
            'weight' => 'required|integer|min:1|max:100',
        ]);

        $criterion = EvaluationCriterion_hr1::create($validated);
        return response()->json($criterion, 201);
    }

    public function destroy($id)
    {
        $criterion = EvaluationCriterion_hr1::findOrFail($id);
        $criterion->delete();
        return response()->json(['message' => 'Criterion deleted successfully']);
    }

    public function questionSets()
    {
        $questionSets = DB::table('question_sets_hr1')
            ->leftJoin('questions_hr1', 'question_sets_hr1.id', '=', 'questions_hr1.question_set_id')
            ->select('question_sets_hr1.*')
            ->groupBy('question_sets_hr1.id')
            ->get()
            ->map(function($qs) {
                $qs->questions = DB::table('questions_hr1')->where('question_set_id', $qs->id)->get();
                return $qs;
            });
        return response()->json($questionSets);
    }

    public function storeQuestionSet(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:assessment,evaluation,survey,interview',
            'questions' => 'nullable',
        ]);

        $questions = $this->decodeQuestionsPayload($request->input('questions'));
        $allowedTypes = ['text', 'multiple-choice', 'rating', 'yes-no', 'file-upload'];

        $questionSetId = DB::transaction(function () use ($validated, $questions, $allowedTypes) {
            $qsId = DB::table('question_sets_hr1')->insertGetId([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'] ?? 'assessment',
                'is_active' => true,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $order = 0;
            foreach ($questions as $q) {
                if (!is_array($q)) {
                    continue;
                }
                $text = trim((string) ($q['question_text'] ?? ''));
                if ($text === '') {
                    continue;
                }
                $type = (string) ($q['question_type'] ?? 'text');
                if (!in_array($type, $allowedTypes, true)) {
                    $type = 'text';
                }
                $options = $this->normalizeQuestionOptions($q['options'] ?? null);

                DB::table('questions_hr1')->insert([
                    'question_set_id' => $qsId,
                    'question_text' => $text,
                    'question_type' => $type,
                    'options' => $options,
                    'is_required' => isset($q['is_required']) ? (bool) $q['is_required'] : true,
                    'order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $qsId;
        });

        $questionSetData = DB::table('question_sets_hr1')->where('id', $questionSetId)->first();
        $questionSetData->questions = DB::table('questions_hr1')->where('question_set_id', $questionSetId)->orderBy('order')->get();
        
        return response()->json($questionSetData, 201);
    }

    public function updateQuestionSet(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:assessment,evaluation,survey,interview',
            'is_active' => 'sometimes|boolean',
            'questions' => 'nullable',
        ]);

        $hasQuestions = $request->has('questions');
        $questions = $this->decodeQuestionsPayload($request->input('questions'));
        $allowedTypes = ['text', 'multiple-choice', 'rating', 'yes-no', 'file-upload'];

        DB::transaction(function () use ($validated, $id, $hasQuestions, $questions, $allowedTypes) {
            $update = $validated;
            unset($update['questions']);

            if (!empty($update)) {
                DB::table('question_sets_hr1')
                    ->where('id', $id)
                    ->update(array_merge($update, ['updated_at' => now()]));
            }

            if (!$hasQuestions) {
                return;
            }

            $keepIds = [];
            $order = 0;
            foreach ($questions as $q) {
                if (!is_array($q)) {
                    continue;
                }

                $text = trim((string) ($q['question_text'] ?? ''));
                if ($text === '') {
                    continue;
                }

                $type = (string) ($q['question_type'] ?? 'text');
                if (!in_array($type, $allowedTypes, true)) {
                    $type = 'text';
                }

                $options = $this->normalizeQuestionOptions($q['options'] ?? null);
                $isRequired = isset($q['is_required']) ? (bool) $q['is_required'] : true;

                $qid = isset($q['id']) ? (int) $q['id'] : null;
                if ($qid) {
                    $updated = DB::table('questions_hr1')
                        ->where('id', $qid)
                        ->where('question_set_id', $id)
                        ->update([
                            'question_text' => $text,
                            'question_type' => $type,
                            'options' => $options,
                            'is_required' => $isRequired,
                            'order' => $order++,
                            'updated_at' => now(),
                        ]);

                    if ($updated) {
                        $keepIds[] = $qid;
                        continue;
                    }
                }

                $newId = DB::table('questions_hr1')->insertGetId([
                    'question_set_id' => $id,
                    'question_text' => $text,
                    'question_type' => $type,
                    'options' => $options,
                    'is_required' => $isRequired,
                    'order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $keepIds[] = $newId;
            }

            // Remove questions not present anymore
            DB::table('questions_hr1')
                ->where('question_set_id', $id)
                ->when(count($keepIds) > 0, fn ($q) => $q->whereNotIn('id', $keepIds))
                ->delete();
        });

        $questionSet = DB::table('question_sets_hr1')->where('id', $id)->first();
        $questionSet->questions = DB::table('questions_hr1')->where('question_set_id', $id)->orderBy('order')->get();
        
        return response()->json($questionSet);
    }

    public function destroyQuestionSet($id)
    {
        DB::table('question_sets_hr1')->where('id', $id)->delete();
        return response()->json(['message' => 'Question set deleted successfully']);
    }

    public function assignToJob(Request $request, $id)
    {
        $validated = $request->validate([
            'job_posting_id' => 'required|exists:job_postings_hr1,id',
        ]);

        $qs = DB::table('question_sets_hr1')->where('id', $id)->first();
        if (!$qs) {
            return response()->json(['error' => 'Question set not found'], 404);
        }

        DB::table('question_sets_hr1')
            ->where('id', $id)
            ->update([
                'job_posting_id' => $validated['job_posting_id'],
                'updated_at' => now(),
            ]);

        $updated = DB::table('question_sets_hr1')->where('id', $id)->first();
        $updated->questions = DB::table('questions_hr1')->where('question_set_id', $id)->get();
        return response()->json($updated);
    }

    public function submitAssessment(Request $request, $id)
    {
        $questionSet = DB::table('question_sets_hr1')->where('id', $id)->first();
        if (!$questionSet) {
            return response()->json(['error' => 'Question set not found'], 404);
        }

        $userId = auth()->id() ?? 5; // Fallback for testing
        $responses = [];
        
        // Get all questions for this set
        $questions = DB::table('questions_hr1')->where('question_set_id', $id)->get();
        
        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            $responseValue = $request->input($fieldName);
            
            if ($responseValue) {
                // Check if response already exists
                $existing = DB::table('applicant_responses_hr1')
                    ->where('user_id', $userId)
                    ->where('question_id', $question->id)
                    ->first();
                
                if ($existing) {
                    // Update existing response
                    DB::table('applicant_responses_hr1')
                        ->where('id', $existing->id)
                        ->update([
                            'response_text' => $question->question_type === 'text' ? $responseValue : null,
                            'response_value' => $question->question_type !== 'text' ? $responseValue : null,
                            'submitted_at' => now()
                        ]);
                } else {
                    // Create new response
                    DB::table('applicant_responses_hr1')->insert([
                        'user_id' => $userId,
                        'question_id' => $question->id,
                        'question_set_id' => $id,
                        'response_text' => $question->question_type === 'text' ? $responseValue : null,
                        'response_value' => $question->question_type !== 'text' ? $responseValue : null,
                        'submitted_at' => now()
                    ]);
                }
                
                $responses[] = [
                    'question_id' => $question->id,
                    'response' => $responseValue
                ];
            }
        }
        
        // Calculate progress
        $totalQuestions = $questions->count();
        $answeredQuestions = DB::table('applicant_responses_hr1')
            ->where('user_id', $userId)
            ->where('question_set_id', $id)
            ->count();
        $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
        $completed = $answeredQuestions === $totalQuestions && $totalQuestions > 0;
        
        return response()->json([
            'message' => 'Assessment submitted successfully',
            'responses' => $responses,
            'progress' => $progress,
            'completed' => $completed
        ]);
    }
}

