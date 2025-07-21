{{-- File: resources/views/courses/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - IEHSAS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .course-card {
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="gradient-bg min-h-screen">
        <!-- Navigation -->
        <nav class="glass-effect border-b border-white border-opacity-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="text-white text-2xl font-bold">
                        <i class="fas fa-graduation-cap mr-2"></i>IEHSAS
                    </div>
                    <div class="space-x-4">
                        <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition duration-300">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
                        @auth
                            <a href="{{ route('courses.my-courses') }}" class="text-white hover:text-gray-200 transition duration-300">
                                <i class="fas fa-book mr-1"></i>My Courses
                            </a>
                            <a href="{{ route('logout') }}" class="text-white hover:text-gray-200 transition duration-300"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-white hover:text-gray-200 transition duration-300">
                                <i class="fas fa-sign-in-alt mr-1"></i>Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-white mb-4">
                    <i class="fas fa-book-open mr-3"></i>Our Courses
                </h1>
                <p class="text-xl text-white opacity-90 max-w-3xl mx-auto">
                    Explore our comprehensive range of professional courses designed to enhance your skills and advance your career.
                </p>
            </div>

            <!-- Courses Coming Soon -->
            <div class="max-w-4xl mx-auto">
                <div class="glass-effect rounded-3xl p-12 text-center">
                    <div class="text-8xl text-blue-500 mb-6">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">
                        Courses Coming Soon!
                    </h2>
                    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                        We're working hard to bring you the best professional courses. Our comprehensive learning platform will be available soon with exciting courses across various domains.
                    </p>
                    
                    <!-- Features Preview -->
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-xl p-6">
                            <div class="text-3xl text-blue-500 mb-3">
                                <i class="fas fa-video"></i>
                            </div>
                            <h3 class="font-bold text-blue-800 mb-2">Video Lectures</h3>
                            <p class="text-blue-600 text-sm">High-quality video content from expert instructors</p>
                        </div>

                        <div class="bg-green-50 rounded-xl p-6">
                            <div class="text-3xl text-green-500 mb-3">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h3 class="font-bold text-green-800 mb-2">Certifications</h3>
                            <p class="text-green-600 text-sm">Industry-recognized certificates upon completion</p>
                        </div>

                        <div class="bg-purple-50 rounded-xl p-6">
                            <div class="text-3xl text-purple-500 mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="font-bold text-purple-800 mb-2">Expert Support</h3>
                            <p class="text-purple-600 text-sm">24/7 support from qualified instructors</p>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="space-y-4">
                        <p class="text-gray-700 font-semibold">
                            Ready to start your learning journey? Take our entry test first!
                        </p>
                        <div class="space-x-4">
                            <a href="{{ route('entry-test.introduction') }}" 
                               class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition duration-300 shadow-lg">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Take Entry Test
                            </a>
                            <a href="{{ route('home') }}" 
                               class="inline-block bg-white text-gray-800 px-8 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition duration-300 shadow-lg">
                                <i class="fas fa-home mr-2"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Signup -->
            <div class="max-w-2xl mx-auto mt-12">
                <div class="glass-effect rounded-2xl p-8 text-center">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-bell mr-2"></i>Get Notified
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Be the first to know when our courses are available. Subscribe to our newsletter for updates.
                    </p>
                    <form class="flex flex-col sm:flex-row gap-4">
                        <input type="email" 
                               placeholder="Enter your email address" 
                               class="flex-1 px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transition duration-300">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>