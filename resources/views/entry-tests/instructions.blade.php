{{-- File: resources/views/entry-test/instructions.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Instructions - {{ $entryTest->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="gradient-bg">
        <!-- Navigation -->
        <nav class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg border-b border-white border-opacity-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="text-white text-2xl font-bold">
                        <i class="fas fa-graduation-cap mr-2"></i>IEHSAS
                    </div>
                    <div class="text-white">
                        <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full">
                            <i class="fas fa-user mr-2"></i>{{ $student->full_name }}
                        </span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="min-h-screen flex items-center justify-center px-4 py-8">
            <div class="max-w-4xl mx-auto w-full">
                <div class="glass-effect rounded-3xl p-8 md:p-12">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="text-5xl text-blue-500 mb-4">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                            Test Instructions
                        </h1>
                        <h2 class="text-xl text-blue-600 font-semibold">
                            {{ $entryTest->title }}
                        </h2>
                    </div>

                    <!-- Student Information -->
                    <div class="bg-blue-50 rounded-xl p-6 mb-8">
                        <h3 class="text-lg font-bold text-blue-800 mb-3">
                            <i class="fas fa-user-check mr-2"></i>Student Information
                        </h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <strong>Name:</strong> {{ $student->full_name }}<br>
                                <strong>Email:</strong> {{ $student->email }}<br>
                                <strong>Contact:</strong> {{ $student->contact_number }}
                            </div>
                            <div>
                                <strong>CNIC:</strong> {{ $student->cnic }}<br>
                                <strong>Gender:</strong> {{ ucfirst($student->gender) }}<br>
                                <strong>Qualification:</strong> {{ $student->qualification }}
                            </div>
                        </div>
                    </div>

                    <!-- Test Information -->
                    <div class="grid md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <div class="text-2xl text-green-500 mb-2">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="font-bold text-green-800">Duration</h4>
                            <p class="text-green-600">{{ $entryTest->duration_minutes }} minutes</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="text-2xl text-blue-500 mb-2">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h4 class="font-bold text-blue-800">Questions</h4>
                            <p class="text-blue-600">{{ $entryTest->questions->count() }} MCQs</p>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-4 text-center">
                            <div class="text-2xl text-purple-500 mb-2">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h4 class="font-bold text-purple-800">Passing Score</h4>
                            <p class="text-purple-600">{{ $entryTest->passing_score }}%</p>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-4 text-center">
                            <div class="text-2xl text-orange-500 mb-2">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h4 class="font-bold text-orange-800">Proctored</h4>
                            <p class="text-orange-600">Yes</p>
                        </div>
                    </div>

                    <!-- Important Instructions -->
                    <div class="space-y-6 mb-8">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-xl">
                            <h4 class="font-bold text-yellow-800 mb-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Important Notice
                            </h4>
                            <p class="text-yellow-700">
                                This test requires webcam and microphone access for proctoring purposes. 
                                You cannot proceed without enabling camera permissions.
                            </p>
                        </div>

                        <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-r-xl">
                            <h4 class="font-bold text-red-800 mb-3">
                                <i class="fas fa-ban mr-2"></i>Proctoring Rules
                            </h4>
                            <ul class="text-red-700 space-y-1">
                                <li><i class="fas fa-times mr-2"></i>Do not switch tabs or minimize the browser</li>
                                <li><i class="fas fa-times mr-2"></i>Keep your face visible to the camera at all times</li>
                                <li><i class="fas fa-times mr-2"></i>Do not use any external help or materials</li>
                                <li><i class="fas fa-times mr-2"></i>Do not communicate with others during the test</li>
                            </ul>
                            <p class="text-red-700 mt-3 font-semibold">
                                Violations will be recorded and may result in test disqualification.
                            </p>
                        </div>

                        <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-r-xl">
                            <h4 class="font-bold text-green-800 mb-3">
                                <i class="fas fa-check-circle mr-2"></i>Test Instructions
                            </h4>
                            <ul class="text-green-700 space-y-1">
                                <li><i class="fas fa-check mr-2"></i>Read each question carefully before answering</li>
                                <li><i class="fas fa-check mr-2"></i>Select only one answer per question</li>
                                <li><i class="fas fa-check mr-2"></i>You can navigate between questions freely</li>
                                <li><i class="fas fa-check mr-2"></i>Submit your test before time runs out</li>
                                <li><i class="fas fa-check mr-2"></i>Ensure stable internet connection</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Camera Permission Section -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8 text-center">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-camera mr-2"></i>Camera & Microphone Setup
                        </h4>
                        <p class="text-gray-600 mb-4">
                            Click the button below to enable camera and microphone access.
                        </p>
                        <button type="button" id="requestPermissions" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition duration-300 mb-4">
                            <i class="fas fa-video mr-2"></i>Enable Camera & Microphone
                        </button>
                        <div id="permissionStatus" class="text-sm"></div>
                    </div>

                    <!-- Start Test Section -->
                    <div class="text-center">
                        <form action="{{ route('entry-test.start', $entryTest->id) }}" method="POST" id="startTestForm">
                            @csrf
                            <button type="submit" id="startTest" disabled
                                    class="bg-gradient-to-r from-green-500 to-blue-600 text-white px-8 py-4 rounded-full text-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed hover:from-green-600 hover:to-blue-700 transform hover:scale-105 transition duration-300 shadow-lg">
                                <i class="fas fa-play mr-2"></i>
                                Start Test
                            </button>
                        </form>
                        <p class="text-gray-600 mt-4 text-sm">
                            Once you start the test, the timer will begin immediately.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const requestBtn = document.getElementById('requestPermissions');
            const startBtn = document.getElementById('startTest');
            const statusDiv = document.getElementById('permissionStatus');

            requestBtn.addEventListener('click', async function() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: true, 
                        audio: true 
                    });
                    
                    statusDiv.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Camera and microphone access granted</span>';
                    startBtn.disabled = false;
                    startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    requestBtn.textContent = 'Permissions Granted';
                    requestBtn.disabled = true;
                    requestBtn.classList.add('bg-green-500');
                    requestBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                    
                    // Stop the stream for now
                    stream.getTracks().forEach(track => track.stop());
                } catch (error) {
                    statusDiv.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>Please allow camera and microphone access to continue</span>';
                    console.error('Error accessing media devices:', error);
                }
            });

            // Prevent form submission without permissions
            document.getElementById('startTestForm').addEventListener('submit', function(e) {
                if (startBtn.disabled) {
                    e.preventDefault();
                    alert('Please enable camera and microphone access first.');
                }
            });
        });
    </script>
</body>
</html>