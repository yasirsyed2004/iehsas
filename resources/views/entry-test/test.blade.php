{{-- File: resources/views/entry-test/test.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entryTest->title }} - Take Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .test-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px;
            min-height: calc(100vh - 40px);
        }

        .test-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .timer {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .timer.warning {
            background: rgba(255,193,7,0.2);
            animation: pulse 1s infinite;
        }

        .timer.danger {
            background: rgba(220,53,69,0.2);
            animation: pulse 0.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .question-sidebar {
            background: #f8f9fa;
            padding: 20px;
            border-right: 2px solid #dee2e6;
            min-height: calc(100vh - 120px);
        }

        .question-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .question-nav-btn {
            width: 45px;
            height: 45px;
            border: 2px solid #dee2e6;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .question-nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .question-nav-btn.current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .question-nav-btn.answered {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .question-nav-btn.marked {
            background: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }

        .question-content {
            padding: 30px;
        }

        .question-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .question-text {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .option-item {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .option-item:hover {
            border-color: #007bff;
            background: #f0f8ff;
        }

        .option-item.selected {
            border-color: #007bff;
            background: #e3f2fd;
        }

        .option-radio {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-top: 2px solid #dee2e6;
            background: #f8f9fa;
        }

        .btn {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .progress-indicator {
            background: #e9ecef;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-bar-custom {
            background: linear-gradient(90deg, #28a745, #20c997);
            height: 100%;
            transition: width 0.3s ease;
        }

        .violation-warning {
            background: linear-gradient(135deg, #ff6b6b, #ffa726);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        /* Camera Preview */
        .camera-preview {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 160px;
            height: 120px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 1050;
            border: 3px solid #28a745;
            background: #000;
        }
        .camera-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }
        .camera-status {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #28a745;
            box-shadow: 0 0 6px #28a745;
        }
        .camera-status.offline { background: #dc3545; box-shadow: 0 0 6px #dc3545; }

        /* Camera Off Overlay */
        .camera-off-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.85);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }
        .camera-off-overlay.active { display: flex; }

        /* Tab Switch Warning Modal */
        .tab-switch-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(220,53,69,0.9);
            z-index: 1999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }
        .tab-switch-overlay.active { display: flex; }

        /* Face detection warning */
        .face-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
            max-width: 320px;
            display: none;
        }
        .face-warning.active { display: block; }

        .test-info {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .test-container {
                margin: 10px;
                min-height: calc(100vh - 20px);
            }
            
            .test-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .question-nav-grid {
                grid-template-columns: repeat(auto-fill, minmax(35px, 1fr));
            }
            
            .question-nav-btn {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="test-container">
        <!-- Header -->
        <div class="test-header">
            <div>
                <h3 class="mb-1">{{ $entryTest->title }}</h3>
                <p class="mb-0 opacity-75">Question <span id="currentQuestionNum">1</span> of {{ $entryTest->questions->count() }}</p>
            </div>
            <div class="timer" id="timer">
                <i class="fas fa-clock me-2"></i>
                <span id="timeDisplay">{{ $entryTest->duration_minutes }}:00</span>
            </div>
        </div>

        <!-- Violation Warning -->
        <div class="violation-warning" id="violationWarning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="violationMessage">Warning: Suspicious activity detected!</span>
        </div>

        <div class="row g-0">
            <!-- Question Navigation Sidebar -->
            <div class="col-md-3">
                <div class="question-sidebar">
                    <!-- Test Info -->
                    <div class="test-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Questions:</span>
                            <span>{{ $entryTest->questions->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Duration:</span>
                            <span>{{ $entryTest->duration_minutes }} min</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Passing:</span>
                            <span>{{ $entryTest->passing_score }}%</span>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Progress</small>
                            <small class="text-muted"><span id="answeredCount">0</span>/{{ $entryTest->questions->count() }}</small>
                        </div>
                        <div class="progress-indicator">
                            <div class="progress-bar-custom" id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Legend:</h6>
                        <div class="d-flex align-items-center mb-1">
                            <div class="question-nav-btn current me-2" style="width: 20px; height: 20px; font-size: 0.7rem;">1</div>
                            <small>Current</small>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <div class="question-nav-btn answered me-2" style="width: 20px; height: 20px; font-size: 0.7rem;">2</div>
                            <small>Answered</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="question-nav-btn me-2" style="width: 20px; height: 20px; font-size: 0.7rem;">3</div>
                            <small>Not Visited</small>
                        </div>
                    </div>

                    <!-- Question Navigation -->
                    <h6 class="text-muted">Questions</h6>
                    <div class="question-nav-grid" id="questionNavGrid">
                        @foreach($entryTest->questions as $index => $question)
                            <button class="question-nav-btn {{ $index === 0 ? 'current' : '' }}" 
                                    data-question="{{ $index }}" 
                                    type="button">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Question Content -->
            <div class="col-md-9">
                <div class="question-content">
                    @foreach($entryTest->questions as $index => $question)
                        <div class="question-slide {{ $index === 0 ? '' : 'd-none' }}" data-question="{{ $index }}">
                            <div class="question-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="text-primary">Question {{ $index + 1 }}</h5>
                                    <span class="badge bg-info">{{ $question->marks }} mark(s)</span>
                                </div>
                                
                                <div class="question-text">
                                    {{ $question->question_text }}
                                </div>

                                @if(is_array($question->options))
                                    <div class="options-container">
                                        @foreach($question->options as $optionIndex => $option)
                                            <div class="option-item" data-value="{{ $option }}">
                                                <input type="radio" 
                                                       name="question_{{ $question->id }}" 
                                                       value="{{ $option }}" 
                                                       id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                                       class="option-radio">
                                                <label for="q{{ $question->id }}_opt{{ $optionIndex }}" class="mb-0 flex-grow-1">
                                                    <span class="fw-bold me-2">{{ chr(65 + $optionIndex) }}.</span>
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="navigation-buttons">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled>
                        <i class="fas fa-arrow-left me-2"></i>Previous
                    </button>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning" id="markBtn">
                            <i class="fas fa-flag me-2"></i>Mark for Review
                        </button>
                        <button type="button" class="btn btn-danger" id="submitTest">
                            <i class="fas fa-paper-plane me-2"></i>Submit Test
                        </button>
                    </div>
                    
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Next<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Test</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to submit your test?</p>
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> Once submitted, you cannot change your answers.
                    </div>
                    <div id="submitSummary">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmSubmit">
                        <i class="fas fa-paper-plane me-2"></i>Submit Test
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Preview -->
    <div class="camera-preview" id="cameraPreview">
        <video id="cameraVideo" autoplay muted playsinline></video>
        <div class="camera-status" id="cameraStatus"></div>
    </div>

    <!-- Camera Off Overlay -->
    <div class="camera-off-overlay" id="cameraOffOverlay">
        <i class="fas fa-video-slash fa-4x mb-4"></i>
        <h2 class="mb-3">Camera Disconnected</h2>
        <p class="mb-2">Please re-enable your camera to continue the exam.</p>
        <p class="text-warning fs-5"><i class="fas fa-clock me-2"></i>Auto-submit in <span id="cameraCountdown">60</span> seconds</p>
    </div>

    <!-- Tab Switch Warning Overlay -->
    <div class="tab-switch-overlay" id="tabSwitchOverlay">
        <i class="fas fa-exclamation-triangle fa-4x mb-4"></i>
        <h2 class="mb-3">WARNING</h2>
        <p class="fs-5 mb-4">Switching tabs again will automatically close<br>your examination paper and submit it.</p>
        <button class="btn btn-light btn-lg" id="tabSwitchDismiss">
            <i class="fas fa-arrow-left me-2"></i>Return to Exam
        </button>
    </div>

    <!-- Face Detection Warning -->
    <div class="face-warning" id="faceWarning">
        <div class="alert alert-danger shadow-lg mb-0">
            <i class="fas fa-user-slash me-2"></i>
            <span id="faceWarningMessage">Please ensure your face is visible to the camera.</span>
        </div>
    </div>

    <!-- Hidden canvas for screenshots -->
    <canvas id="screenshotCanvas" style="display:none;"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        class TestManager {
            constructor() {
                this.currentQuestion = 0;
                this.totalQuestions = {{ $entryTest->questions->count() }};
                this.attemptId = {{ $attempt->id }};
                this.timeRemaining = Math.max(0, {{ $attempt->expires_at->timestamp }} - {{ now()->timestamp }});
                this.answers = {};
                this.violations = 0;
                this.questions = @json($entryTest->questions);
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                // Security state
                this.tabSwitchCount = 0;
                this.cameraStream = null;
                this.timerPaused = false;
                this.cameraOffTimer = null;
                this.cameraCountdown = 60;
                this.screenshotInterval = null;
                this.faceDetectionInterval = null;
                this.noFaceSeconds = 0;
                this.faceDetectionReady = false;
                this.isSubmitting = false;

                this.init();
            }

            init() {
                this.startTimer();
                this.bindEvents();
                this.initializeCamera();
                this.initializeProctoring();
                this.updateQuestionDisplay();
                this.loadSavedAnswers();
            }

            // ==================== TIMER ====================
            startTimer() {
                this.timerInterval = setInterval(() => {
                    if (this.timerPaused) return;
                    this.timeRemaining--;
                    this.updateTimerDisplay();
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        this.autoSubmit();
                    }
                }, 1000);
            }

            pauseTimer() { this.timerPaused = true; }
            resumeTimer() { this.timerPaused = false; }

            updateTimerDisplay() {
                const minutes = Math.floor(this.timeRemaining / 60);
                const seconds = this.timeRemaining % 60;
                document.getElementById('timeDisplay').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                const timer = document.getElementById('timer');
                if (this.timeRemaining <= 300) timer.classList.add('danger');
                else if (this.timeRemaining <= 600) timer.classList.add('warning');
            }

            // ==================== CAMERA ====================
            async initializeCamera() {
                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                    const video = document.getElementById('cameraVideo');
                    video.srcObject = this.cameraStream;

                    // Monitor camera track
                    const videoTrack = this.cameraStream.getVideoTracks()[0];
                    if (videoTrack) {
                        videoTrack.onended = () => this.handleCameraOff();
                        videoTrack.onmute = () => this.handleCameraOff();
                        videoTrack.onunmute = () => this.handleCameraOn();
                    }

                    document.getElementById('cameraStatus').classList.remove('offline');
                    this.startScreenshotCapture();
                    this.initFaceDetection();
                } catch (error) {
                    console.error('Camera init failed:', error);
                    this.handleCameraOff();
                }
            }

            handleCameraOff() {
                document.getElementById('cameraStatus').classList.add('offline');
                document.querySelector('.camera-preview').style.borderColor = '#dc3545';
                this.recordViolation('camera_off', 'Camera was turned off or disconnected');
                this.showViolationWarning('Camera disconnected! Please re-enable your camera.');
                this.pauseTimer();

                // Show overlay with countdown
                this.cameraCountdown = 60;
                document.getElementById('cameraCountdown').textContent = this.cameraCountdown;
                document.getElementById('cameraOffOverlay').classList.add('active');

                this.cameraOffTimer = setInterval(() => {
                    this.cameraCountdown--;
                    document.getElementById('cameraCountdown').textContent = this.cameraCountdown;
                    if (this.cameraCountdown <= 0) {
                        clearInterval(this.cameraOffTimer);
                        this.recordViolation('camera_off_timeout', 'Camera remained off for 60 seconds - auto-submitted');
                        this.submitTest();
                    }
                }, 1000);
            }

            handleCameraOn() {
                document.getElementById('cameraStatus').classList.remove('offline');
                document.querySelector('.camera-preview').style.borderColor = '#28a745';
                document.getElementById('cameraOffOverlay').classList.remove('active');
                if (this.cameraOffTimer) {
                    clearInterval(this.cameraOffTimer);
                    this.cameraOffTimer = null;
                }
                this.resumeTimer();
            }

            // ==================== PROCTORING ====================
            initializeProctoring() {
                // Tab switching - 2-strike policy
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden && !this.isSubmitting) {
                        this.tabSwitchCount++;
                        this.recordViolation('tab_switch', `Tab switch #${this.tabSwitchCount}`);

                        if (this.tabSwitchCount === 1) {
                            // 1st strike: Show prominent warning
                            document.getElementById('tabSwitchOverlay').classList.add('active');
                        } else if (this.tabSwitchCount >= 2) {
                            // 2nd strike: Auto-submit
                            document.getElementById('tabSwitchOverlay').classList.remove('active');
                            this.recordViolation('tab_switch_disqualified', 'Exam auto-submitted due to 2nd tab switch');
                            this.submitTest();
                        }
                    }
                });

                // Dismiss tab switch warning
                document.getElementById('tabSwitchDismiss').addEventListener('click', () => {
                    document.getElementById('tabSwitchOverlay').classList.remove('active');
                });

                // Fullscreen exit detection
                document.addEventListener('fullscreenchange', () => {
                    if (!document.fullscreenElement) {
                        this.recordViolation('fullscreen_exit', 'User exited fullscreen mode');
                    }
                });

                // Right-click prevention
                document.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    this.recordViolation('right_click', 'User attempted right-click');
                    this.showViolationWarning('Right-click is not allowed during the exam!');
                });

                // Keyboard shortcut prevention
                document.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && ['c','v','u','s','p'].includes(e.key.toLowerCase())) {
                        e.preventDefault();
                        this.recordViolation('keyboard_shortcut', `Blocked: Ctrl+${e.key.toUpperCase()}`);
                    }
                    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['i','j','c'].includes(e.key.toLowerCase()))) {
                        e.preventDefault();
                        this.recordViolation('dev_tools', 'Attempted to open developer tools');
                    }
                });
            }

            // ==================== SCREENSHOT CAPTURE ====================
            startScreenshotCapture() {
                const captureScreenshot = () => {
                    if (this.isSubmitting) return;
                    const video = document.getElementById('cameraVideo');
                    const canvas = document.getElementById('screenshotCanvas');
                    if (!video || video.readyState < 2) return;

                    canvas.width = 320;
                    canvas.height = 240;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, 320, 240);

                    const imageData = canvas.toDataURL('image/jpeg', 0.6);
                    this.sendScreenshot(imageData);

                    // Schedule next capture at random interval (30-90 seconds)
                    const nextInterval = (30 + Math.floor(Math.random() * 60)) * 1000;
                    this.screenshotInterval = setTimeout(captureScreenshot, nextInterval);
                };

                // First screenshot after 10 seconds
                this.screenshotInterval = setTimeout(captureScreenshot, 10000);
            }

            async sendScreenshot(imageData) {
                try {
                    await fetch(`/entry-test/attempt/${this.attemptId}/screenshot`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ image: imageData })
                    });
                } catch (error) {
                    console.error('Failed to send screenshot:', error);
                }
            }

            // ==================== FACE DETECTION ====================
            async initFaceDetection() {
                try {
                    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model';
                    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                    this.faceDetectionReady = true;
                    this.startFaceDetection();
                } catch (error) {
                    console.error('Face detection init failed:', error);
                }
            }

            startFaceDetection() {
                if (!this.faceDetectionReady) return;

                this.faceDetectionInterval = setInterval(async () => {
                    if (this.isSubmitting || this.timerPaused) return;

                    const video = document.getElementById('cameraVideo');
                    if (!video || video.readyState < 2) return;

                    try {
                        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }));

                        if (detections.length === 0) {
                            this.noFaceSeconds += 5;
                            if (this.noFaceSeconds >= 10 && this.noFaceSeconds < 30) {
                                this.showFaceWarning('Please ensure your face is visible to the camera.');
                            }
                            if (this.noFaceSeconds >= 30) {
                                this.hideFaceWarning();
                                this.recordViolation('face_not_detected', 'No face detected for 30+ seconds - auto-submitted');
                                this.submitTest();
                                return;
                            }
                        } else if (detections.length > 1) {
                            this.noFaceSeconds = 0;
                            this.showFaceWarning('Multiple faces detected! Only the test-taker should be visible.');
                            this.recordViolation('multiple_faces', `${detections.length} faces detected`);
                        } else {
                            // Exactly 1 face — good
                            this.noFaceSeconds = 0;
                            this.hideFaceWarning();
                        }
                    } catch (error) {
                        console.error('Face detection error:', error);
                    }
                }, 5000);
            }

            showFaceWarning(message) {
                document.getElementById('faceWarningMessage').textContent = message;
                document.getElementById('faceWarning').classList.add('active');
            }

            hideFaceWarning() {
                document.getElementById('faceWarning').classList.remove('active');
            }

            // ==================== VIOLATIONS ====================
            async recordViolation(type, details) {
                this.violations++;
                try {
                    await fetch(`{{ route('entry-test.track-violation', $attempt->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ type, details })
                    });
                } catch (error) {
                    console.error('Failed to record violation:', error);
                }
            }

            showViolationWarning(message) {
                const warning = document.getElementById('violationWarning');
                document.getElementById('violationMessage').textContent = message;
                warning.style.display = 'block';
                setTimeout(() => { warning.style.display = 'none'; }, 3000);
            }

            // ==================== QUESTION NAVIGATION ====================
            bindEvents() {
                document.getElementById('prevBtn').addEventListener('click', () => {
                    if (this.currentQuestion > 0) {
                        this.saveCurrentAnswer();
                        this.currentQuestion--;
                        this.updateQuestionDisplay();
                    }
                });

                document.getElementById('nextBtn').addEventListener('click', () => {
                    this.saveCurrentAnswer();
                    if (this.currentQuestion < this.totalQuestions - 1) {
                        this.currentQuestion++;
                        this.updateQuestionDisplay();
                    } else {
                        this.showSubmitModal();
                    }
                });

                document.querySelectorAll('.question-nav-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.saveCurrentAnswer();
                        this.currentQuestion = parseInt(btn.dataset.question);
                        this.updateQuestionDisplay();
                    });
                });

                document.getElementById('submitTest').addEventListener('click', () => this.showSubmitModal());
                document.getElementById('confirmSubmit').addEventListener('click', () => this.submitTest());

                document.addEventListener('change', (e) => {
                    if (e.target.type === 'radio') {
                        const option = e.target.closest('.option-item');
                        const slide = e.target.closest('.question-slide');
                        slide.querySelectorAll('.option-item').forEach(opt => opt.classList.remove('selected'));
                        option.classList.add('selected');
                        this.saveCurrentAnswer();
                    }
                });
            }

            updateQuestionDisplay() {
                document.querySelectorAll('.question-slide').forEach((slide, index) => {
                    slide.classList.toggle('d-none', index !== this.currentQuestion);
                });
                document.getElementById('prevBtn').disabled = this.currentQuestion === 0;
                document.getElementById('nextBtn').textContent =
                    this.currentQuestion === this.totalQuestions - 1 ? 'Submit Test' : 'Next';
                document.getElementById('currentQuestionNum').textContent = this.currentQuestion + 1;
                document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
                    btn.classList.remove('current');
                    if (index === this.currentQuestion) btn.classList.add('current');
                });
                this.loadCurrentAnswer();
                this.updateProgress();
            }

            saveCurrentAnswer() {
                const currentSlide = document.querySelector(`.question-slide[data-question="${this.currentQuestion}"]`);
                const selectedOption = currentSlide.querySelector('input[type="radio"]:checked');
                if (selectedOption) {
                    const questionId = this.questions[this.currentQuestion].id;
                    this.answers[questionId] = selectedOption.value;
                    const navBtn = document.querySelector(`.question-nav-btn[data-question="${this.currentQuestion}"]`);
                    navBtn.classList.add('answered');
                    this.saveAnswerToServer(questionId, selectedOption.value);
                }
            }

            loadCurrentAnswer() {
                const questionId = this.questions[this.currentQuestion].id;
                const savedAnswer = this.answers[questionId];
                if (savedAnswer) {
                    const currentSlide = document.querySelector(`.question-slide[data-question="${this.currentQuestion}"]`);
                    const option = currentSlide.querySelector(`input[value="${savedAnswer}"]`);
                    if (option) {
                        option.checked = true;
                        option.closest('.option-item').classList.add('selected');
                    }
                }
            }

            async saveAnswerToServer(questionId, selectedAnswer) {
                try {
                    await fetch(`{{ route('entry-test.submit-answer', $attempt->id) }}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                        body: JSON.stringify({ question_id: questionId, selected_answer: selectedAnswer })
                    });
                } catch (error) { console.error('Failed to save answer:', error); }
            }

            async loadSavedAnswers() {}

            updateProgress() {
                const answeredCount = Object.keys(this.answers).length;
                document.getElementById('answeredCount').textContent = answeredCount;
                document.getElementById('progressBar').style.width = `${(answeredCount / this.totalQuestions) * 100}%`;
            }

            showSubmitModal() {
                this.saveCurrentAnswer();
                const answeredCount = Object.keys(this.answers).length;
                document.getElementById('submitSummary').innerHTML = `
                    <div class="row">
                        <div class="col-6"><strong>Answered:</strong> ${answeredCount}</div>
                        <div class="col-6"><strong>Unanswered:</strong> ${this.totalQuestions - answeredCount}</div>
                    </div>`;
                new bootstrap.Modal(document.getElementById('submitModal')).show();
            }

            // ==================== SUBMIT ====================
            async submitTest() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;

                // Cleanup
                clearInterval(this.timerInterval);
                if (this.screenshotInterval) clearTimeout(this.screenshotInterval);
                if (this.faceDetectionInterval) clearInterval(this.faceDetectionInterval);
                if (this.cameraOffTimer) clearInterval(this.cameraOffTimer);
                if (this.cameraStream) {
                    this.cameraStream.getTracks().forEach(track => track.stop());
                }

                try {
                    const submitBtn = document.getElementById('confirmSubmit');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
                        submitBtn.disabled = true;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('entry-test.submit', $attempt->id) }}`;
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = this.csrfToken;
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                } catch (error) {
                    console.error('Failed to submit test:', error);
                    this.isSubmitting = false;
                }
            }

            autoSubmit() {
                alert('Time is up! Your test will be submitted automatically.');
                this.submitTest();
            }
        }

        document.addEventListener('DOMContentLoaded', () => { new TestManager(); });
        window.addEventListener('beforeunload', (e) => { e.preventDefault(); e.returnValue = ''; });
    </script>
</body>
</html>