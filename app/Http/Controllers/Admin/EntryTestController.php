<?php
// File: app/Http/Controllers/Admin/EntryTestController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntryTest;
use App\Models\EntryTestQuestion;
use App\Models\EntryTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryTestController extends Controller
{
    public function index()
    {
        $entryTests = EntryTest::withCount(['questions', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.entry-tests.index', compact('entryTests'));
    }

    public function create()
    {
        return view('admin.entry-tests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:180',
            'total_questions' => 'required|integer|min:1|max:100',
            'passing_score' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $entryTest = EntryTest::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'total_questions' => $request->total_questions,
            'passing_score' => $request->passing_score,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->route('admin.entry-tests.show', $entryTest)
            ->with('success', 'Entry test created successfully!');
    }

    public function show(EntryTest $entryTest)
    {
        $entryTest->load(['questions', 'attempts.student']);
        
        $stats = [
            'total_questions' => $entryTest->questions()->count(),
            'total_attempts' => $entryTest->attempts()->count(),
            'completed_attempts' => $entryTest->attempts()->where('status', 'completed')->count(),
            'passed_attempts' => $entryTest->attempts()->completed()->passed()->count(),
            'average_score' => $entryTest->attempts()->completed()->avg('percentage')
        ];

        return view('admin.entry-tests.show', compact('entryTest', 'stats'));
    }

    public function edit(EntryTest $entryTest)
    {
        return view('admin.entry-tests.edit', compact('entryTest'));
    }

    public function update(Request $request, EntryTest $entryTest)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:180',
            'total_questions' => 'required|integer|min:1|max:100',
            'passing_score' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $entryTest->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'total_questions' => $request->total_questions,
            'passing_score' => $request->passing_score,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->route('admin.entry-tests.show', $entryTest)
            ->with('success', 'Entry test updated successfully!');
    }

    public function destroy(EntryTest $entryTest)
    {
        // Check if test has attempts
        if ($entryTest->attempts()->exists()) {
            return back()->with('error', 'Cannot delete entry test with existing attempts.');
        }

        $entryTest->delete();

        return redirect()->route('admin.entry-tests.index')
            ->with('success', 'Entry test deleted successfully!');
    }

    public function toggleStatus(EntryTest $entryTest)
    {
        // Only allow one active test at a time
        if (!$entryTest->is_active) {
            EntryTest::where('is_active', true)->update(['is_active' => false]);
        }

        $entryTest->update(['is_active' => !$entryTest->is_active]);

        $status = $entryTest->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Entry test {$status} successfully!");
    }
}