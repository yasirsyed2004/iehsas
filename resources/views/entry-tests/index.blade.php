{{-- File: resources/views/entry-test/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry Test Information - IEHSAS</title>
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

        .slide-up {
            animation: slideUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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
                        <a href="{{ route('entry-test.introduction') }}" class="text-white hover:text-gray-200 transition duration-300">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Introduction
                        </a>
                        <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition duration-300">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="min-h-screen flex items-center justify-center px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Main Card -->
                <div class="glass-effect rounded-3xl p-8 md:p-12 slide-up">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="text-6xl text-blue-500 mb-4">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                            {{ $entryTest->title }}
                        </h1>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            {{ $entryTest->description }}
                        </p>
                    </div>

                    <!-- Test Information -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-xl p-6 text-center card-hover">
                            <div class="text-3xl text-blue-500 mb-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="font-bold text-blue-800 mb-1">Duration</h3>
                            <p class="text-blue-600 text-lg font-semibold">{{ $entryTest->duration_minutes }} minutes</p>
                        </div>

                        <div class="bg-green-50 rounded-xl p-6 text-center card-hover">
                            <div class="text-3xl text-green-500 mb-3">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h3 class="font-bold text-green-800 mb-1">Questions</h3>
                            <p class="text-green-600 text-lg font-semibold">{{ $entryTest->total_questions }} MCQs</p>
                        </div>

                        <div class="bg-purple-50 rounded-xl p-6 text-center card-hover">
                            <div class="text-3xl text-purple-500 mb-3">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3 class="font-bold text-purple-800 mb-1">Passing Score</h3>
                            <p class="text-purple-600 text-lg font-semibold">{{ $entryTest->passing_score }}%</p>
                        </div>

                        <div class="bg-orange-50 rounded-xl p-6 text-center card-hover">
                            <div class="text-3xl text-orange-500 mb-3">
                                <i class="fas fa-redo"></i>
                            </div>
                            <h3 class="font-bold text-orange-800 mb-1">Attempts</h3>
                            <p class="text-orange-600 text-lg font-semibold">1 per CNIC</p>
                        </div>
                    </div>

                    <!-- Important Instructions -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-r-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-bold text-yellow-800 mb-2">Important Instructions</h3>
                                <ul class="text-yellow-700 space-y-1">
                                    <li><i class="fas fa-check mr-2"></i>You can only attempt this test once with your CNIC</li>
                                    <li><i class="fas fa-check mr-2"></i>Camera and microphone access is required for proctoring</li>
                                    <li><i class="fas fa-check mr-2"></i>Do not switch tabs or minimize the browser during the test</li>
                                    <li><i class="fas fa-check mr-2"></i>Make sure you have a stable internet connection</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-blue-800 mb-2">
                                <i class="fas fa-user-edit mr-2"></i>Student Registration Required
                            </h3>
                            <p class="text-blue-700 mb-4">
                                Before starting the test, you need to provide your personal information for record keeping and verification purposes.
                            </p>
                            <div class="text-sm text-blue-600">
                                <strong>Required Information:</strong> Full Name, Email, Contact Number, CNIC, Gender, Qualification
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="text-center">
                        <a href="{{ route('entry-test.register') }}" 
                           class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition duration-300 shadow-lg">
                            <i class="fas fa-user-plus mr-2"></i>
                            Register for Entry Test
                        </a>
                        <p class="text-gray-600 mt-4 text-sm">
                            By proceeding, you agree to our terms and conditions for the entry test.
                        </p>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-8 grid md:grid-cols-3 gap-6">
                    <div class="glass-effect rounded-xl p-6 text-center card-hover">
                        <div class="text-3xl text-green-500 mb-3">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">Secure Testing</h3>
                        <p class="text-gray-600 text-sm">Advanced proctoring ensures fair assessment</p>
                    </div>

                    <div class="glass-effect rounded-xl p-6 text-center card-hover">
                        <div class="text-3xl text-blue-500 mb-3">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">Online Platform</h3>
                        <p class="text-gray-600 text-sm">Take the test from comfort of your home</p>
                    </div>

                    <div class="glass-effect rounded-xl p-6 text-center card-hover">
                        <div class="text-3xl text-purple-500 mb-3">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">Course Access</h3>
                        <p class="text-gray-600 text-sm">Qualify for our professional courses</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Trigger animations
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const elements = document.querySelectorAll('.slide-up');
                elements.forEach((el, index) => {
                    setTimeout(() => {
                        el.style.animationDelay = '0s';
                        el.classList.add('slide-up');
                    }, index * 200);
                });
            }, 100);
        });
    </script>
</body>
</html>