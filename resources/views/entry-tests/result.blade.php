<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Test Result - {{ $attempt->entryTest->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Result Summary -->
                    <div class="text-center mb-8">
                        @if($passed)
                            <div class="mb-4">
                                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-green-600">Congratulations!</h3>
                                <p class="text-lg text-gray-600">You have passed the entry test</p>
                            </div>
                        @else
                            <div class="mb-4">
                                <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-red-600">Better Luck Next Time</h3>
                                <p class="text-lg text-gray-600">You need to improve your score</p>
                            </div>
                        @endif
                    </div>

                    <!-- Score Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg text-center">
                            <h4 class="text-lg font-semibold text-blue-800">Score</h4>
                            <p class="text-3xl font-bold text-blue-600">
                                {{ $attempt->obtained_marks }}/{{ $attempt->total_marks }}
                            </p>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg text-center">
                            <h4 class="text-lg font-semibold text-purple-800">Percentage</h4>
                            <p class="text-3xl font-bold text-purple-600">
                                {{ round($attempt->percentage, 1) }}%
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <h4 class="text-lg font-semibold text-gray-800">Duration</h4>
                            <p class="text-3xl font-bold text-gray-600">
                                {{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}
                            </p>
                        </div>
                    </div>

                    <!-- Detailed Results -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold mb-4">Question-wise Results</h4>
                        <div class="space-y-4">
                            @foreach($attempt->answers as $answer)
                                <div class="border rounded-lg p-4 {{ $answer->is_correct ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-medium mb-2">{{ $answer->question->question_text }}</p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Your Answer:</strong> {{ $answer->selected_answer }}
                                            </p>
                                            @if(!$answer->is_correct)
                                                <p class="text-sm text-green-600">
                                                    <strong>Correct Answer:</strong> {{ $answer->question->correct_answer }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            @if($answer->is_correct)
                                                <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">
                                                    ✓ Correct
                                                </span>
                                            @else
                                                <span class="bg-red-500 text-white px-2 py-1 rounded text-sm">
                                                    ✗ Wrong
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Proctoring Report -->
                    @if($attempt->proctoring_violations && count($attempt->proctoring_violations) > 0)
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold mb-4 text-orange-800">Proctoring Report</h4>
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <p class="text-orange-800 mb-2">
                                    <strong>Total Violations:</strong> {{ count($attempt->proctoring_violations) }}
                                </p>
                                <p class="text-orange-800 mb-4">
                                    <strong>Browser Switches:</strong> {{ $attempt->browser_switches ?? 0 }}
                                </p>
                                
                                <div class="space-y-2">
                                    @foreach($attempt->proctoring_violations as $violation)
                                        <div class="text-sm text-orange-700">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $violation['type'])) }}:</strong>
                                            {{ $violation['details'] }}
                                            <span class="text-gray-500">
                                                ({{ \Carbon\Carbon::parse($violation['timestamp'])->format('H:i:s') }})
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="text-center">
                        @if($passed)
                            <div class="mb-4">
                                <p class="text-green-600 font-medium">
                                    You are now eligible for course enrollment!
                                </p>
                            </div>
                            <a href="{{ route('dashboard') }}" 
                               class="bg-green-500 hover:bg-green-700 text-white px-6 py-3 rounded-lg inline-block">
                                Browse Courses
                            </a>
                        @else
                            <div class="mb-4">
                                <p class="text-red-600 font-medium">
                                    Minimum passing score: {{ $attempt->entryTest->passing_score }}%
                                </p>
                                <p class="text-gray-600">
                                    Please prepare more and try again later.
                                </p>
                            </div>
                            <a href="{{ route('entry-tests.index') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-block">
                               Back to Tests
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>