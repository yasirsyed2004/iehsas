{{-- File: resources/views/entry-test/introduction.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry Test Introduction - IEHSAS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Gradient Background */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        /* Animations */
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
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
        
        /* Video Container - Proper 16:9 Aspect Ratio */
        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: #000;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* Glass Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        /* Welcome Text Animations */
        .welcome-title {
            background: linear-gradient(45deg, #1e40af, #3b82f6, #6366f1, #8b5cf6);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease-in-out infinite;
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        /* Button Hover Effects */
        .btn-primary {
            background: linear-gradient(135deg, #10b981, #3b82f6);
            color: white;
            font-weight: bold;
            padding: 16px 32px;
            border-radius: 50px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            transform: translateY(0);
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #059669, #2563eb);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-primary:disabled:hover {
            transform: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Status Badge */
        .status-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 8px 16px;
            border-radius: 25px;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
        }
        
        /* Main Container */
        .main-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .content-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            margin-bottom: 32px;
        }
        
        .section-padding {
            padding: 32px;
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
                    <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition duration-300">
                        <i class="fas fa-home mr-1"></i>Home
                    </a>
                </div>
            </div>
        </nav>

        <div class="main-container" style="padding-top: 48px; padding-bottom: 48px;">
            <!-- Welcome Section -->
            <div class="glass-effect content-section slide-up">
                <div class="section-padding" style="text-align: center;">
                    <div class="floating-animation" style="margin-bottom: 16px;">
                        <i class="fas fa-clipboard-check text-6xl text-blue-500"></i>
                    </div>
                    <h1 class="welcome-title">
                        Entry Test Introduction
                    </h1>
                    <p class="text-xl text-gray-600 mb-4">
                        Welcome to IEHSAS Entry Test System. Please watch the safety training video below before proceeding.
                    </p>
                </div>
            </div>

            <!-- Main Content Section -->
            <div class="content-section slide-up">
                <div class="section-padding">
                    <!-- Video Section -->
                    <div style="text-align: center; margin-bottom: 48px;">
                        <h2 style="font-size: 2rem; font-weight: bold; margin-bottom: 8px; color: #1f2937;">
                            Safety Training Video
                        </h2>
                        <p style="font-size: 1.125rem; color: #6b7280; margin-bottom: 32px;">
                            Please watch this important safety training video before proceeding to the entry test.
                        </p>
                        
                        <!-- YouTube Video Container -->
                        <div style="max-width: 1024px; margin: 0 auto 32px auto;">
                            <div class="video-container">
                                <iframe 
                                    src="https://www.youtube.com/embed/L0Yy46xLUCw?enablejsapi=1&rel=0&modestbranding=1" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen
                                    id="youtube-video"
                                    title="Safety Training Video">
                                </iframe>
                            </div>
                        </div>

                        <!-- Video Status Indicator -->
                        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 32px;">
                            <div class="status-badge">
                                <svg class="pulse-animation" style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Please complete the video before proceeding</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Section -->
                    <div style="text-align: center;">
                        <div style="background: linear-gradient(135deg, #eff6ff, #e0e7ff); border-radius: 16px; padding: 32px; margin-bottom: 32px;">
                            <h3 style="font-size: 1.5rem; font-weight: bold; color: #1f2937; margin-bottom: 16px;">
                                Ready for the Next Step?
                            </h3>
                            <p style="color: #6b7280; margin-bottom: 24px; max-width: 512px; margin-left: auto; margin-right: auto;">
                                Once you've completed watching the safety training video, you can proceed to the entry test registration. 
                                Make sure you understand all the safety protocols demonstrated in the video.
                            </p>
                            
                            <!-- Enhanced Start Test Button -->
                            <div style="display: flex; flex-direction: column; gap: 16px; justify-content: center; align-items: center;">
                                <button 
                                    id="start-test-btn"
                                    onclick="checkVideoCompletion()"
                                    class="btn-primary"
                                    disabled>
                                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                    Ready to Start Entry Test
                                </button>
                                
                                <div style="font-size: 0.875rem; color: #6b7280; display: flex; align-items: center;">
                                    <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Duration: 25 minutes | Questions: 20 | Passing Score: 60%
                                </div>
                            </div>
                        </div>

                        <!-- Information Cards -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-top: 32px;">
                            <div style="background: white; border: 2px solid #dbeafe; border-radius: 12px; padding: 24px; text-align: center;">
                                <div style="color: #3b82f6; margin-bottom: 16px;">
                                    <i class="fas fa-shield-alt text-4xl"></i>
                                </div>
                                <h4 style="font-weight: bold; color: #1f2937; margin-bottom: 8px;">Safety First</h4>
                                <p style="color: #6b7280; font-size: 0.875rem;">Learn essential safety protocols and procedures</p>
                            </div>
                            
                            <div style="background: white; border: 2px solid #dcfce7; border-radius: 12px; padding: 24px; text-align: center;">
                                <div style="color: #10b981; margin-bottom: 16px;">
                                    <i class="fas fa-user-graduate text-4xl"></i>
                                </div>
                                <h4 style="font-weight: bold; color: #1f2937; margin-bottom: 8px;">Expert Training</h4>
                                <p style="color: #6b7280; font-size: 0.875rem;">Professional guidance from industry experts</p>
                            </div>
                            
                            <div style="background: white; border: 2px solid #f3e8ff; border-radius: 12px; padding: 24px; text-align: center;">
                                <div style="color: #8b5cf6; margin-bottom: 16px;">
                                    <i class="fas fa-certificate text-4xl"></i>
                                </div>
                                <h4 style="font-weight: bold; color: #1f2937; margin-bottom: 8px;">Certification</h4>
                                <p style="color: #6b7280; font-size: 0.875rem;">Earn your safety certification upon completion</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let videoWatched = false;
        let player;
        
        // Load YouTube API
        function loadYouTubeAPI() {
            if (window.YT) {
                onYouTubeIframeAPIReady();
                return;
            }
            
            const tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }
        
        // YouTube API Ready
        window.onYouTubeIframeAPIReady = function() {
            player = new YT.Player('youtube-video', {
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }
        
        function onPlayerReady(event) {
            console.log('YouTube player is ready');
        }
        
        function onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                videoWatched = true;
                updateButtonState();
                showCompletionMessage();
            }
        }
        
        function updateButtonState() {
            const button = document.getElementById('start-test-btn');
            if (videoWatched) {
                button.disabled = false;
                button.innerHTML = `
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Ready to Start Entry Test!
                `;
            }
        }
        
        function showCompletionMessage() {
            alert('Great! You have completed the safety training video. You can now proceed to the entry test.');
        }
        
        function checkVideoCompletion() {
            if (videoWatched) {
                window.location.href = "{{ route('entry-test.index') }}";
            } else {
                alert('Please watch the complete safety training video before proceeding to the test.');
                document.getElementById('youtube-video').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadYouTubeAPI();
            
            // Trigger animations
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