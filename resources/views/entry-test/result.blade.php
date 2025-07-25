{{-- File: resources/views/entry-test/result.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results - {{ $attempt->entryTest->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .result-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
            overflow: hidden;
        }

        .result-header {
            background: linear-gradient(135deg, {{ $passed ? '#28a745' : '#dc3545' }} 0%, {{ $passed ? '#20c997' : '#fd7e14' }} 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .result-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .result-content {
            padding: 40px 30px;
        }

        .stat-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .question-review {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .correct-answer {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }

        .wrong-answer {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .student-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .btn {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .certificate-section {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4a 100%);
            color: #333;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .result-container {
                margin: 10px;
            }
            
            .result-header {
                padding: 30px 20px;
            }
            
            .result-content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="result-container">
        <!-- Result Header -->
        <div class="result-header">
            <div class="result-icon">
                @if($passed)
                    <i class="fas fa-trophy"></i>
                @else
                    <i class="fas fa-times-circle"></i>
                @endif
            </div>
            
            <h1 class="mb-3">
                @if($passed)
                    Congratulations!
                @else
                    Test Completed
                @endif
            </h1>
            
            <p class="lead mb-4">
                @if($passed)
                    You have successfully passed the {{ $attempt->entryTest->title }}
                @else
                    You have completed the {{ $attempt->entryTest->title }}
                @endif
            </p>

            <div class="score-circle">
                {{ number_format($attempt->percentage, 1) }}%
            </div>

            <h3 class="mb-0">
                @if($passed)
                    <i class="fas fa-check-circle me-2"></i>PASSED
                @else
                    <i class="fas fa-times-circle me-2"></i>NOT PASSED
                @endif
            </h3>
        </div>

        <!-- Result Content -->
        <div class="result-content">
            <!-- Student Information -->
            <div class="student-info">
                <h4 class="mb-3"><i class="fas fa-user me-2"></i>Student Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Name:</strong> {{ $attempt->student->full_name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $attempt->student->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>CNIC:</strong> {{ $attempt->student->cnic ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Test Date:</strong> {{ $attempt->completed_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Test Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number text-primary">{{ $attempt->obtained_marks }}</div>
                        <div class="stat-label">Marks Obtained</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number text-info">{{ $attempt->total_marks }}</div>
                        <div class="stat-label">Total Marks</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number text-success">{{ $attempt->answers->where('is_correct', true)->count() }}</div>
                        <div class="stat-label">Correct Answers</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number text-danger">{{ $attempt->answers->where('is_correct', false)->count() }}</div>
                        <div class="stat-label">Wrong Answers</div>
                    </div>
                </div>
            </div>

            <!-- Test Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="fas fa-info-circle me-2"></i>Test Details</h5>
                    <ul class="list-unstyled">
                        <li><strong>Duration:</strong> {{ $attempt->entryTest->duration_minutes }} minutes</li>
                        <li><strong>Total Questions:</strong> {{ $attempt->entryTest->total_questions }}</li>
                        <li><strong>Passing Score:</strong> {{ $attempt->entryTest->passing_score }}%</li>
                        <li><strong>Your Score:</strong> {{ number_format($attempt->percentage, 1) }}%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-clock me-2"></i>Time Information</h5>
                    <ul class="list-unstyled">
                        <li><strong>Started:</strong> {{ $attempt->started_at->format('M j, Y g:i A') }}</li>
                        <li><strong>Completed:</strong> {{ $attempt->completed_at->format('M j, Y g:i A') }}</li>
                        <li><strong>Time Taken:</strong> {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} minutes</li>
                        <li><strong>Status:</strong> 
                            <span class="badge {{ $passed ? 'bg-success' : 'bg-danger' }}">
                                {{ $passed ? 'Passed' : 'Failed' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            @if($passed)
                <!-- Certificate Section -->
                <div class="certificate-section">
                    <h4><i class="fas fa-certificate me-2"></i>Certificate Eligible</h4>
                    <p class="mb-3">Congratulations! You are eligible to receive a certificate for completing this test successfully.</p>
                    <button class="btn btn-dark" onclick="window.print()">
                        <i class="fas fa-download me-2"></i>Download Certificate
                    </button>
                </div>
            @else
                <!-- Retake Information -->
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>What's Next?</h5>
                    <p class="mb-2">You need {{ $attempt->entryTest->passing_score }}% to pass this test. Your score was {{ number_format($attempt->percentage, 1) }}%.</p>
                    <p class="mb-0">Contact the administration if you believe you should be allowed a retake opportunity.</p>
                </div>
            @endif

            <!-- Question Review (Optional - can be toggled) -->
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-list-alt me-2"></i>Question Review</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="toggleReview()" id="toggleBtn">
                        <i class="fas fa-eye me-1"></i>Show Details
                    </button>
                </div>
                
                <div id="questionReview" style="display: none;">
                    @foreach($attempt->answers as $index => $answer)
                        <div class="question-review {{ $answer->is_correct ? 'correct-answer' : 'wrong-answer' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                                <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }}">
                                    {{ $answer->is_correct ? 'Correct' : 'Wrong' }}
                                    ({{ $answer->marks_obtained }}/{{ $answer->question->marks ?? 0 }} marks)
                                </span>
                            </div>
                            <p class="mb-2"><strong>{{ $answer->question->question_text ?? 'Question not found' }}</strong></p>
                            <p class="mb-1"><strong>Your Answer:</strong> {{ $answer->selected_answer ?? 'No answer' }}</p>
                            @if(!$answer->is_correct)
                                <p class="mb-0 text-success"><strong>Correct Answer:</strong> {{ $answer->question->correct_answer ?? 'N/A' }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4 pt-4 border-top">
                <a href="{{ route('entry-test.index') }}" class="btn btn-primary me-3">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Results
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleReview() {
            const review = document.getElementById('questionReview');
            const btn = document.getElementById('toggleBtn');
            
            if (review.style.display === 'none') {
                review.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-eye-slash me-1"></i>Hide Details';
            } else {
                review.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-eye me-1"></i>Show Details';
            }
        }

        // Confetti animation for passed results
        @if($passed)
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.top = '-10px';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.borderRadius = '50%';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.animation = 'fall 3s linear forwards';
                    
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 3000);
                }, i * 100);
            }
        }

        // Add CSS for confetti animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Start confetti after page load
        window.addEventListener('load', () => {
            setTimeout(createConfetti, 500);
        });
        @endif

        // Prevent going back to test
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>