<?php
// File: app/Http/Controllers/Admin/QuestionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntryTest;
use App\Models\EntryTestQuestion;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = EntryTestQuestion::with('entryTest')
            ->orderBy('entry_test_id')
            ->orderBy('order')
            ->paginate(15);

        $entryTests = EntryTest::all();

        return view('admin.questions.index', compact('questions', 'entryTests'));
    }

    public function create()
    {
        $entryTests = EntryTest::all();
        return view('admin.questions.create', compact('entryTests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_test_id' => 'required|exists:entry_tests,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string|max:255',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1|max:10',
            'order' => 'nullable|integer|min:1'
        ]);

        // Validate that correct_answer is in options
        if (!in_array($request->correct_answer, $request->options)) {
            return back()->withErrors(['correct_answer' => 'Correct answer must be one of the options.'])
                ->withInput();
        }

        // Auto-assign order if not provided
        $order = $request->order;
        if (!$order) {
            $order = EntryTestQuestion::where('entry_test_id', $request->entry_test_id)
                ->max('order') + 1;
        }

        EntryTestQuestion::create([
            'entry_test_id' => $request->entry_test_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $request->options,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'order' => $order
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully!');
    }

    public function show(EntryTestQuestion $question)
    {
        $question->load('entryTest');
        return view('admin.questions.show', compact('question'));
    }

    public function edit(EntryTestQuestion $question)
    {
        $entryTests = EntryTest::all();
        return view('admin.questions.edit', compact('question', 'entryTests'));
    }

    public function update(Request $request, EntryTestQuestion $question)
    {
        $request->validate([
            'entry_test_id' => 'required|exists:entry_tests,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string|max:255',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1|max:10',
            'order' => 'nullable|integer|min:1'
        ]);

        // Validate that correct_answer is in options
        if (!in_array($request->correct_answer, $request->options)) {
            return back()->withErrors(['correct_answer' => 'Correct answer must be one of the options.'])
                ->withInput();
        }

        $question->update([
            'entry_test_id' => $request->entry_test_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $request->options,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'order' => $request->order ?: $question->order
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(EntryTestQuestion $question)
    {
        // Check if question has been answered in any attempts
        $hasAnswers = $question->answers()->exists();
        
        if ($hasAnswers) {
            return back()->with('error', 'Cannot delete question that has been answered in test attempts.');
        }

        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully!');
    }
}