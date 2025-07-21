<?php

namespace App\Http\Controllers;

use App\Models\EntryTest;
use App\Models\EntryTestAttempt;
use App\Models\EntryTestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntryTestController extends Controller
{
    public function index()
    {
        $entryTests = EntryTest::where('is_active', true)->get();
        $userAttempts = Auth::user()->entryTestAttempts()->pluck('entry_test_id')->toArray();
        
        return view('entry-tests.index', compact('entryTests', 'userAttempts'));
    }

    public function show($id)
    {
        $entryTest = EntryTest::findOrFail($id);
        
        // Check if user already attempted
        $existingAttempt = Auth::user()->entryTestAttempts()
            ->where('entry_test_id', $id)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('entry-tests.result', $existingAttempt->id);
        }

        return view('entry-tests.instructions', compact('entryTest'));
    }

    public function start($id)
    {
        $entryTest = EntryTest::findOrFail($id);
        
        // Check if user already attempted
        $existingAttempt = Auth::user()->entryTestAttempts()
            ->where('entry_test_id', $id)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('entry-tests.result', $existingAttempt->id);
        }
        
        // Create new attempt
        $attempt = EntryTestAttempt::create([
            'user_id' => Auth::id(),
            'entry_test_id' => $id,
            'started_at' => now(),
            'expires_at' => now()->addMinutes($entryTest->duration_minutes),
            'status' => 'in_progress'
        ]);

        // Redirect to the test taking interface
        return redirect()->route('entry-tests.take', [
            'id' => $entryTest->id,
            'attemptId' => $attempt->id
        ]);
    }

    // Add this new method for displaying the test interface
    public function take($id, $attemptId)
    {
        $entryTest = EntryTest::with('questions')->findOrFail($id);
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        
        // Security checks
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'in_progress') {
            return redirect()->route('entry-tests.index')
                ->with('error', 'Invalid test attempt.');
        }

        // Check if test has expired (if you have expiration logic)
        if (isset($attempt->expires_at) && now()->greaterThan($attempt->expires_at)) {
            $attempt->update(['status' => 'expired']);
            return redirect()->route('entry-tests.index')
                ->with('error', 'Test time has expired.');
        }

        // This should load your test interface (the blade file you showed me)
        return view('entry-tests.test', compact('entryTest', 'attempt'));
    }

    public function submitAnswer(Request $request, $attemptId)
    {
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $question = $attempt->entryTest->questions()
            ->findOrFail($request->question_id);

        $isCorrect = $request->selected_answer === $question->correct_answer;
        
        EntryTestAnswer::updateOrCreate([
            'entry_test_attempt_id' => $attemptId,
            'entry_test_question_id' => $request->question_id,
        ], [
            'selected_answer' => $request->selected_answer,
            'is_correct' => $isCorrect,
            'marks_obtained' => $isCorrect ? $question->marks : 0
        ]);

        return response()->json(['success' => true]);
    }

    public function submit($attemptId)
    {
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Calculate results
        $totalMarks = $attempt->entryTest->questions()->sum('marks');
        $obtainedMarks = $attempt->answers()->sum('marks_obtained');
        $percentage = $totalMarks > 0 ? ($obtainedMarks / $totalMarks) * 100 : 0;

        $attempt->update([
            'completed_at' => now(),
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
            'status' => 'completed'
        ]);

        return redirect()->route('entry-tests.result', $attempt->id);
    }

    public function result($attemptId)
    {
        $attempt = EntryTestAttempt::with(['entryTest', 'answers.question'])
            ->findOrFail($attemptId);

        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $passed = $attempt->percentage >= $attempt->entryTest->passing_score;
        return view('entry-tests.result', compact('attempt', 'passed'));
    }

    public function trackViolation(Request $request, $attemptId)
    {
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $violations = $attempt->proctoring_violations ?? [];
        $violations[] = [
            'type' => $request->type,
            'timestamp' => now(),
            'details' => $request->details
        ];

        $attempt->update([
            'proctoring_violations' => $violations,
            'browser_switches' => $attempt->browser_switches + 1
        ]);

        return response()->json(['success' => true]);
    }
}