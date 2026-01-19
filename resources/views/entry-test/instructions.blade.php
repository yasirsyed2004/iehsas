{{-- File: resources/views/entry-test/instructions.blade.php --}}
{{-- FIXED: Updated to use new ID fields instead of old CNIC field --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Instructions - IEHSAS Entry Test</title>
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
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
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
                    <div class="space-x-4">
                        <a href="{{ route('entry-test.index') }}" class="text-white hover:text-gray-200 transition duration-300">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Test Info
                        </a>
                        <a href="https://www.iehsas.com/" target="_blank" class="text-white hover:text-gray-200 transition duration-300">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
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
                                {{-- FIXED: Display ID Type and Number instead of CNIC --}}
                                <strong>{{ $student->id_type_label }}:</strong> {{ $student->formatted_id }}<br>
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
                                <i class="fas fa-lightbulb mr-2"></i>Tips for Success
                            </h4>
                            <ul class="text-green-700 space-y-1">
                                <li><i class="fas fa-check mr-2"></i>Read each question carefully before answering</li>
                                <li><i class="fas fa-check mr-2"></i>Manage your time effectively</li>
                                <li><i class="fas fa-check mr-2"></i>Review your answers before submitting</li>
                                <li><i class="fas fa-check mr-2"></i>Stay calm and focused throughout the test</li>
                            </ul>
                        </div>
                    </div>

                    <!-- System Requirements -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h4 class="font-bold text-gray-800 mb-3">
                            <i class="fas fa-desktop mr-2"></i>System Requirements
                        </h4>
                        <div class="grid md:grid-cols-2 gap-4 text-gray-700">
                            <div>
                                <ul class="space-y-1">
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>Modern web browser (Chrome, Firefox, Safari)</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>Stable internet connection</li>
                                </ul>
                            </div>
                            <div>
                                <ul class="space-y-1">
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>Working webcam and microphone</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>Quiet, well-lit environment</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Ready to Start -->
                    <div class="text-center">
                        <form action="{{ route('entry-test.start', $entryTest->id) }}" method="POST" id="startTestForm">
                            @csrf
                            <button type="submit" 
                                    id="startButton"
                                    class="bg-gradient-to-r from-green-500 to-blue-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-green-600 hover:to-blue-700 transform hover:scale-105 transition duration-300 shadow-lg">
                                <i class="fas fa-play-circle mr-2"></i>
                                Start Test Now
                            </button>
                        </form>
                        <p class="text-gray-600 mt-4 text-sm">
                            By starting the test, you agree to follow all the instructions and rules mentioned above.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Request camera permission before starting test
        document.getElementById('startTestForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const startButton = document.getElementById('startButton');
            const originalText = startButton.innerHTML;
            
            try {
                startButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Requesting Camera Access...';
                startButton.disabled = true;
                
                // Request camera and microphone permissions
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: true, 
                    audio: true 
                });
                
                // Stop the stream after getting permission
                stream.getTracks().forEach(track => track.stop());
                
                // If successful, submit the form
                startButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Starting Test...';
                this.submit();
                
            } catch (error) {
                console.error('Camera permission denied:', error);
                startButton.innerHTML = originalText;
                startButton.disabled = false;
                
                alert('Camera and microphone access is required to start the test. Please enable permissions and try again.');
            }
        });
    </script>
</body>
</html>