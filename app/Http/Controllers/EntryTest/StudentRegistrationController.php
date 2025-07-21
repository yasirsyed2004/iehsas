<?php
// File: app/Http/Controllers/EntryTest/StudentRegistrationController.php

namespace App\Http\Controllers\EntryTest;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\EntryTest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentRegistrationController extends Controller
{
    public function showForm()
    {
        $entryTest = EntryTest::where('is_active', true)->first();
        
        if (!$entryTest) {
            return redirect()->route('home')->with('error', 'No active entry test available.');
        }

        return view('entry-test.register', compact('entryTest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'cnic' => [
                'required',
                'string',
                'regex:/^\d{5}-\d{7}-\d{1}$/',
                'unique:students,cnic'
            ],
            'gender' => 'required|in:male,female,other',
            'qualification' => 'required|string|max:255'
        ], [
            'cnic.regex' => 'CNIC must be in format: 12345-1234567-1',
            'cnic.unique' => 'A student with this CNIC has already registered for the test.'
        ]);

        // Check if student with this CNIC already exists and has attempted
        $existingStudent = Student::where('cnic', $request->cnic)->first();
        
        if ($existingStudent && !$existingStudent->canAttemptTest()) {
            return back()->withErrors([
                'cnic' => 'You have already attempted the test. Contact admin for retake permission.'
            ])->withInput();
        }

        // Create or update student
        $student = Student::updateOrCreate(
            ['cnic' => $request->cnic],
            [
                'full_name' => $request->full_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'gender' => $request->gender,
                'qualification' => $request->qualification
            ]
        );

        // Store student ID in session for test flow
        session(['registered_student_id' => $student->id]);

        // Get the active entry test
        $entryTest = EntryTest::where('is_active', true)->first();

        return redirect()->route('entry-test.instructions', $entryTest->id)
            ->with('success', 'Registration successful! Please review the test instructions.');
    }
}