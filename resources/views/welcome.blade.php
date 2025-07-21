{{-- File: resources/views/homepage.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEHSAS - Learning Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .card-hover:hover {
            transform: translateY(-20px) scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
        
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 0 40px rgba(102, 126, 234, 0.8); }
        }
        
        .slide-in-left {
            animation: slideInLeft 1s ease-out forwards;
            opacity: 0;
            transform: translateX(-100px);
        }
        
        .slide-in-right {
            animation: slideInRight 1s ease-out forwards;
            opacity: 0;
            transform: translateX(100px);
        }
        
        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-gradient {
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg">
    <!-- Navigation -->
    <nav class="glass-effect p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-white text-2xl font-bold">
                <i class="fas fa-graduation-cap mr-2"></i>IEHSAS
            </div>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="text-white hover:text-gray-200 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-1"></i>Login
                </a>
                <a href="{{ route('register') }}" class="bg-white text-purple-600 px-4 py-2 rounded-full hover:bg-gray-100 transition duration-300">
                    <i class="fas fa-user-plus mr-1"></i>Register
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Title -->
            <div class="text-center mb-16">
                <h1 class="text-6xl md:text-8xl font-bold text-white mb-6 floating-animation">
                    Welcome to <span class="text-gradient">IEHSAS</span>
                </h1>
                <p class="text-xl md:text-2xl text-white opacity-90 max-w-3xl mx-auto">
                    Your gateway to professional learning and career advancement. Choose your path below.
                </p>
            </div>

            <!-- Two Main Sections -->
            <div class="grid md:grid-cols-2 gap-8 md:gap-16 max-w-6xl mx-auto">
                <!-- Entry Test Section -->
                <div class="slide-in-left">
                    <a href="{{ route('entry-test.introduction') }}" class="block">
                        <div class="card-hover bg-white rounded-3xl p-8 md:p-12 text-center shadow-2xl pulse-glow">
                            <div class="text-6xl md:text-7xl mb-6">
                                <i class="fas fa-clipboard-check text-blue-500"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                                Entry Test
                            </h2>
                            <p class="text-lg text-gray-600 mb-6">
                                Take our comprehensive entry test to assess your knowledge and qualify for our courses.
                            </p>
                            <div class="bg-blue-100 rounded-xl p-4 mb-6">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="font-semibold text-blue-800">Duration</div>
                                        <div class="text-blue-600">25 minutes</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-blue-800">Questions</div>
                                        <div class="text-blue-600">20 MCQs</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-blue-800">Passing Score</div>
                                        <div class="text-blue-600">60%</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-blue-800">Attempts</div>
                                        <div class="text-blue-600">1 per CNIC</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-6 rounded-full font-semibold">
                                Start Entry Test <i class="fas fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Courses Section -->
                <div class="slide-in-right">
                    <a href="{{ route('courses.index') }}" class="block">
                        <div class="card-hover bg-white rounded-3xl p-8 md:p-12 text-center shadow-2xl pulse-glow">
                            <div class="text-6xl md:text-7xl mb-6">
                                <i class="fas fa-book-open text-green-500"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                                Courses
                            </h2>
                            <p class="text-lg text-gray-600 mb-6">
                                Explore our wide range of professional courses designed to enhance your skills and career.
                            </p>
                            <div class="bg-green-100 rounded-xl p-4 mb-6">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="font-semibold text-green-800">Categories</div>
                                        <div class="text-green-600">Multiple</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-green-800">Levels</div>
                                        <div class="text-green-600">All Levels</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-green-800">Certification</div>
                                        <div class="text-green-600">Yes</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-green-800">Support</div>
                                        <div class="text-green-600">24/7</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-6 rounded-full font-semibold">
                                Browse Courses <i class="fas fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-20 text-center">
                <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-white">
                        <div class="text-4xl mb-4">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Secure Testing</h3>
                        <p class="opacity-90">Advanced proctoring system ensures test integrity</p>
                    </div>
                    <div class="text-white">
                        <div class="text-4xl mb-4">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Certified Learning</h3>
                        <p class="opacity-90">Get industry-recognized certifications</p>
                    </div>
                    <div class="text-white">
                        <div class="text-4xl mb-4">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
                        <p class="opacity-90">Round-the-clock assistance for all learners</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="glass-effect mt-20 py-8">
        <div class="max-w-7xl mx-auto text-center text-white">
            <p>&copy; 2025 IEHSAS Learning Management System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add staggered animation delays
        document.addEventListener('DOMContentLoaded', function() {
            const leftElement = document.querySelector('.slide-in-left');
            const rightElement = document.querySelector('.slide-in-right');
            
            setTimeout(() => {
                leftElement.style.animationDelay = '0.2s';
                rightElement.style.animationDelay = '0.4s';
            }, 100);
        });
    </script>
</body>
</html>