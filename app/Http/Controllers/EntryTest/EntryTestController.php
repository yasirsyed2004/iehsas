<?php
// File: app/Http/Controllers/EntryTest/EntryTestController.php

namespace App\Http\Controllers\EntryTest;

use App\Http\Controllers\Controller;
use App\Models\EntryTest;
use App\Models\EntryTestAttempt;
use App\Models\EntryTestAnswer;
use App\Models\ExamScreenshot;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EntryTestController extends Controller
{
    public function introduction()
    {
        return view('entry-test.introduction');
    }

    public function index()
    {
        $entryTest = EntryTest::where('is_active', true)->first();
        
        if (!$entryTest) {
            return redirect()->route('home')->with('error', 'No active entry test available.');
        }

        return view('entry-test.index', compact('entryTest'));
    }

    public function instructions($id)
    {
        $entryTest = EntryTest::with('questions')->findOrFail($id);
        
        // Check if student is registered
        $studentId = session('registered_student_id');
        if (!$studentId) {
            return redirect()->route('entry-test.register')
                ->with('error', 'Please complete registration first.');
        }

        $student = Student::findOrFail($studentId);
        
        // Check if student can attempt test
        if (!$student->canAttemptTest()) {
            return redirect()->route('entry-test.index')
                ->with('error', 'You have already attempted the test. Contact admin for retake permission.');
        }

        // Check eligibility proof
        $canStartTest = !$student->requiresEligibilityProof() || $student->hasRequiredEligibilityProof();

        return view('entry-test.instructions', compact('entryTest', 'student', 'canStartTest'));
    }

    public function start($id)
{
    $entryTest = EntryTest::findOrFail($id);
    $studentId = session('registered_student_id');
    
    if (!$studentId) {
        return redirect()->route('entry-test.register')
            ->with('error', 'Please complete registration first.');
    }

    $student = Student::findOrFail($studentId);

    // Check eligibility proof before allowing test start
    if ($student->requiresEligibilityProof() && !$student->hasRequiredEligibilityProof()) {
        return redirect()->route('entry-test.instructions', $entryTest->id)
            ->with('error', 'You must upload the required eligibility proof before starting the test.');
    }

    // Check if student already has an active attempt
    $existingAttempt = EntryTestAttempt::where('student_id', $student->id)
        ->where('entry_test_id', $id)
        ->where('status', 'in_progress')
        ->first();

    if ($existingAttempt) {
        return redirect()->route('entry-test.take', [$entryTest->id, $existingAttempt->id]);
    }

    // Check if student has completed attempt
    $completedAttempt = EntryTestAttempt::where('student_id', $student->id)
        ->where('entry_test_id', $id)
        ->where('status', 'completed')
        ->first();

    if ($completedAttempt && !$student->is_retake_allowed) {
        return redirect()->route('entry-test.result', $completedAttempt->id);
    }

    // Create new attempt - FIX: Add user_id as null for guest students
    $attempt = EntryTestAttempt::create([
        'user_id' => null,  // FIX: Set to null for guest student attempts
        'student_id' => $student->id,
        'entry_test_id' => $id,
        'started_at' => now(),
        'expires_at' => now()->addMinutes($entryTest->duration_minutes),
        'status' => 'in_progress'
    ]);

    return redirect()->route('entry-test.take', [$entryTest->id, $attempt->id]);
}

    public function take($id, $attemptId)
    {
        $entryTest = EntryTest::with('questions')->findOrFail($id);
        $attempt = EntryTestAttempt::findOrFail($attemptId);
    
        // Security checks
        $studentId = session('registered_student_id');
        if (!$studentId || $attempt->student_id !== $studentId || $attempt->status !== 'in_progress') {
            return redirect()->route('entry-test.index')  // FIX: Changed from entry-tests.index
                ->with('error', 'Invalid test attempt.');
        }

        // Check if test has expired
        if ($attempt->isExpired()) {
            $attempt->update(['status' => 'expired']);
            return redirect()->route('entry-test.index')  // FIX: Changed from entry-tests.index
                ->with('error', 'Test time has expired.');
        }

        return view('entry-test.test', compact('entryTest', 'attempt'));
    }

    public function submitAnswer(Request $request, $attemptId)
    {
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        $studentId = session('registered_student_id');
        
        if (!$studentId || $attempt->student_id !== $studentId) {
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
        $studentId = session('registered_student_id');
        
        if (!$studentId || $attempt->student_id !== $studentId) {
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

        return redirect()->route('entry-test.result', $attempt->id);
    }

    public function result($attemptId)
    {
        $attempt = EntryTestAttempt::with(['entryTest', 'answers.question', 'student'])
            ->findOrFail($attemptId);

        $studentId = session('registered_student_id');
        if (!$studentId || $attempt->student_id !== $studentId) {
            abort(403);
        }

        $passed = $attempt->hasPassed();
        return view('entry-test.result', compact('attempt', 'passed'));
    }

    public function trackViolation(Request $request, $attemptId)
    {
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        $studentId = session('registered_student_id');

        if (!$studentId || $attempt->student_id !== $studentId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $violations = $attempt->proctoring_violations ?? [];
        $violations[] = [
            'type' => $request->type,
            'details' => $request->details,
            'timestamp' => now()->toISOString()
        ];

        $updateData = ['proctoring_violations' => $violations];

        // Track tab switches separately
        if (in_array($request->type, ['tab_switch', 'tab_switch_disqualified'])) {
            $updateData['browser_switches'] = ($attempt->browser_switches ?? 0) + 1;
        }

        // Auto-disqualify on 2nd tab switch
        if ($request->type === 'tab_switch_disqualified') {
            $updateData['status'] = 'disqualified';

            // Notify all active admins
            $admins = Admin::where('status', true)->get();
            $student = Student::find($attempt->student_id);
            $studentName = $student ? $student->full_name : 'Unknown Student';

            foreach ($admins as $admin) {
                Notification::createForAdmin(
                    $admin->id,
                    'exam_disqualified',
                    'Exam Disqualification',
                    "{$studentName} was automatically disqualified from the entry test due to repeated tab switching.",
                    ['attempt_id' => $attempt->id, 'student_id' => $attempt->student_id]
                );
            }
        }

        $attempt->update($updateData);

        return response()->json(['success' => true]);
    }

    public function storeScreenshot(Request $request, $attemptId)
    {
        $attempt = EntryTestAttempt::findOrFail($attemptId);
        $studentId = session('registered_student_id');

        if (!$studentId || $attempt->student_id !== $studentId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $imageData = $request->input('image');
        if (!$imageData || !str_starts_with($imageData, 'data:image/')) {
            return response()->json(['error' => 'Invalid image data'], 422);
        }

        // Decode base64 image
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
        $imageContent = base64_decode($imageData);

        if (!$imageContent) {
            return response()->json(['error' => 'Failed to decode image'], 422);
        }

        $filename = 'screenshot_' . $attemptId . '_' . time() . '_' . mt_rand(1000, 9999) . '.jpg';
        $filePath = 'exam-screenshots/' . $attemptId . '/' . $filename;

        Storage::disk('enrollment')->put($filePath, $imageContent);

        ExamScreenshot::create([
            'entry_test_attempt_id' => $attemptId,
            'file_path' => $filePath,
            'file_size' => strlen($imageContent),
            'captured_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}