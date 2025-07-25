<x-app-layout>
    <div class="min-h-screen bg-gray-100" id="testContainer">
        <!-- Timer and Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h1 class="text-xl font-semibold">{{ $entryTest->title }}</h1>
                    <div class="flex items-center space-x-4">
                        <div id="timer" class="text-lg font-bold text-red-600">
                            {{ $entryTest->duration_minutes }}:00
                        </div>
                        <button id="submitTest" 
                                class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">
                            Submit Test
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Monitoring -->
        <div class="fixed top-4 right-4 z-50">
            <video id="webcam" width="150" height="100" autoplay muted class="border-2 border-red-500 rounded"></video>
        </div>

        <!-- Test Content -->
        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Question Navigation -->
                        <div class="mb-6">
                            <div class="flex flex-wrap gap-2" id="questionNav">
                                @foreach($entryTest->questions as $index => $question)
                                    <button class="question-nav-btn w-10 h-10 border rounded text-sm" 
                                            data-question="{{ $index }}"
                                            id="nav-{{ $index }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Question Container -->
                        <div id="questionContainer">
                            @foreach($entryTest->questions as $index => $question)
                                <div class="question-slide" 
                                     data-question="{{ $index }}"
                                     data-question-id="{{ $question->id }}">
                                    <h3 class="text-lg font-semibold mb-4">
                                        Question {{ $index + 1 }} of {{ $entryTest->questions->count() }}
                                    </h3>
                                    <p class="text-lg mb-6">{{ $question->question_text }}</p>
                                    
                                    <div class="space-y-3">
                                        @foreach($question->options as $optionIndex => $option)
                                            <label class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                                                <input type="radio" 
                                                       name="question_{{ $question->id }}" 
                                                       value="{{ $option }}"
                                                       class="mr-3">
                                                <span>{{ chr(65 + $optionIndex) }}. {{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button id="prevBtn" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Previous
                            </button>
                            <button id="nextBtn" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for submission -->
    <form id="submitForm" action="{{ route('entry-tests.submit', $attempt->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        class EntryTestSystem {
            constructor(attemptId, duration) {
                this.questionContainer = document.getElementById('questionContainer');
                this.attemptId = attemptId;
                this.duration = duration * 60; // Convert to seconds
                this.currentQuestion = 0;
                this.totalQuestions = document.querySelectorAll('.question-slide').length;
                this.answers = {};
                this.violations = 0;
                console.log('currentQuestion', this.currentQuestion);
                console.log('totalQuestions', this.totalQuestions);
                
                this.initializeTest();
                this.startTimer();
                this.initializeProctoring();
                this.bindEvents();
            }

            initializeTest() {
                this.updateQuestionDisplay();
                this.updateNavigation();
            }

            startTimer() {
                const timerElement = document.getElementById('timer');
                
                this.timerInterval = setInterval(() => {
                    this.duration--;
                    
                    const minutes = Math.floor(this.duration / 60);
                    const seconds = this.duration % 60;
                    
                    timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    
                    if (this.duration <= 300) { // Last 5 minutes
                        timerElement.classList.add('text-red-600', 'font-bold');
                    }
                    
                    if (this.duration <= 0) {
                        this.submitTest();
                    }
                }, 1000);
            }

            async initializeProctoring() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: true, 
                        audio: true 
                    });
                    
                    const webcam = document.getElementById('webcam');
                    webcam.srcObject = stream;

                    // Monitor tab visibility
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.recordViolation('tab_switch', 'User switched tabs or minimized browser');
                        }
                    });

                    // Monitor window focus
                    window.addEventListener('blur', () => {
                        this.recordViolation('focus_loss', 'Browser lost focus');
                    });

                    // Prevent right-click
                    document.addEventListener('contextmenu', (e) => {
                        e.preventDefault();
                        this.recordViolation('right_click', 'User attempted right-click');
                    });

                    // Monitor keyboard shortcuts
                    document.addEventListener('keydown', (e) => {
                        if (e.ctrlKey || e.altKey || e.metaKey) {
                            e.preventDefault();
                            this.recordViolation('keyboard_shortcut', `Attempted shortcut: ${e.key}`);
                        }
                    });

                    if(this.violations >= 5){
                        alert('Too many violation detected. Test will be submitted automatically.');
                        this.submitTest();
                    }

                } catch (error) {
                    console.error('Failed to initialize proctoring:', error);
                }
            }

            async recordViolation(type, details) {
                this.violations++;
                
                try {
                    await fetch(`/entry-tests/attempt/${this.attemptId}/violation`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            type: type,
                            details: details
                        })
                    });
                } catch (error) {
                    console.error('Failed to record violation:', error);
                }
            }

            bindEvents() {
                // Navigation buttons
                document.getElementById('prevBtn').addEventListener('click', () => {
                    if (this.currentQuestion > 0) {
                        this.saveCurrentAnswer();
                        this.currentQuestion--;
                        this.updateQuestionDisplay();
                    }
                });

                document.getElementById('nextBtn').addEventListener('click', () => {
                    if (this.currentQuestion < this.totalQuestions - 1) {
                        this.saveCurrentAnswer();
                        this.currentQuestion++;
                        this.updateQuestionDisplay();
                    } else {
                        // On last question, submit the test
                        if (confirm('Are you sure you want to submit the test?')) {
                            this.submitTest();
                        }
                    }
                });

                // Question navigation
                document.querySelectorAll('.question-nav-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.saveCurrentAnswer();
                        this.currentQuestion = parseInt(btn.dataset.question);
                        this.updateQuestionDisplay();
                    });
                });

                // Submit button
                document.getElementById('submitTest').addEventListener('click', () => {
                    if (confirm('Are you sure you want to submit the test?')) {
                        this.submitTest();
                    }
                });

                // Auto-save answers
                document.addEventListener('change', (e) => {
                    if (e.target.type === 'radio') {
                        this.saveCurrentAnswer();
                    }
                });
            }

            updateQuestionDisplay() {
                // Hide all questions
                document.querySelectorAll('.question-slide').forEach(slide => {
                    slide.classList.add('hidden');
                    slide.classList.remove('active');
                });

                // Show current question
                const currentSlide = this.questionContainer.querySelector(`[data-question="${this.currentQuestion}"]`);
                if (currentSlide) {
                    currentSlide.classList.remove('hidden');
                    currentSlide.classList.add('active');
                }

                // Navigation buttons
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');

                // Hide Previous button on first question, show otherwise
                if (this.currentQuestion === 0) {
                    prevBtn.style.display = 'none';
                } else {
                    prevBtn.style.display = '';
                }

                // Hide Next button on last question, show otherwise
                if (this.currentQuestion === this.totalQuestions - 1) {
                    nextBtn.textContent = 'Finish';
                } else {
                    nextBtn.textContent = 'Next';
                }

                // Always show Next button (unless you want to hide it on last question)
                nextBtn.style.display = '';

                // Update navigation indicators
                this.updateNavigation();

                // Load saved answer if exists
                this.loadSavedAnswer();
            }

            updateNavigation() {
                document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
                    btn.classList.remove('bg-blue-500', 'text-white', 'bg-green-500');
                    console.log('triggered ', index);
                    
                    if (index === this.currentQuestion) {
                        btn.classList.add('bg-blue-500', 'text-white');
                    } else if (this.answers[this.getQuestionId(index)]) {
                        btn.classList.add('bg-green-500', 'text-white');
                    }
                });
            }

            getQuestionId(questionIndex = null) {
                const index = questionIndex !== null ? questionIndex : this.currentQuestion;
                const slide = this.questionContainer.querySelector(`[data-question="${index}"]`); // <-- FIXED
                return slide ? slide.dataset.questionId : null;
            }

            async saveCurrentAnswer() {
                const questionId = this.getQuestionId();
                const selectedOption = document.querySelector(`input[name="question_${questionId}"]:checked`);
                
                if (selectedOption) {
                    this.answers[questionId] = selectedOption.value;
                    
                    try {
                        await fetch(`/entry-tests/attempt/${this.attemptId}/answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                question_id: questionId,
                                selected_answer: selectedOption.value
                            })
                        });
                    } catch (error) {
                        console.error('Failed to save answer:', error);
                    }
                }
            }

            loadSavedAnswer() {
                const questionId = this.getQuestionId();
                if (this.answers[questionId]) {
                    const radioBtn = document.querySelector(`input[name="question_${questionId}"][value="${this.answers[questionId]}"]`);
                    if (radioBtn) {
                        radioBtn.checked = true;
                    }
                }
            }

            async submitTest() {
                // Save current answer
                await this.saveCurrentAnswer();
                
                // Stop timer
                clearInterval(this.timerInterval);
                
                // Stop webcam
                const webcam = document.getElementById('webcam');
                if (webcam.srcObject) {
                    webcam.srcObject.getTracks().forEach(track => track.stop());
                }
                
                // Submit form
                document.getElementById('submitForm').submit();
            }
        }

        // Initialize test when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const test = new EntryTestSystem({{ $attempt->id }}, {{ $entryTest->duration_minutes }});
        });
    </script>
</x-app-layout>