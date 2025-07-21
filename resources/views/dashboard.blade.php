<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Ensure Tailwind CSS is loaded -->
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @endpush

    <!-- Custom Styles -->
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
        
        .welcome-subtitle {
            color: #374151;
            font-size: 1.25rem;
            font-weight: 500;
            opacity: 0.8;
        }
        
        .star-icon {
            color: #f59e0b;
            filter: drop-shadow(0 4px 8px rgba(245, 158, 11, 0.3));
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
        
        /* Card Styles */
        .feature-card {
            background: white;
            border: 2px solid;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .feature-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .feature-card.blue {
            border-color: #dbeafe;
        }
        
        .feature-card.green {
            border-color: #dcfce7;
        }
        
        .feature-card.purple {
            border-color: #f3e8ff;
        }
        
        /* Icon styles */
        .icon-blue { color: #3b82f6; }
        .icon-green { color: #10b981; }
        .icon-purple { color: #8b5cf6; }
        .icon-yellow { color: #f59e0b; }
        
        /* Text Shadow */
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
        
        /* Grid Layout */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: 0 16px;
            }
            .section-padding {
                padding: 24px;
            }
            .btn-primary {
                padding: 12px 24px;
                font-size: 16px;
            }
        }
    </style>

    <div class="gradient-bg">
        <div class="main-container" style="padding-top: 48px; padding-bottom: 48px;">
            <!-- Welcome Section -->
            <div class="glass-effect content-section slide-up">
                <div class="section-padding" style="text-align: center;">
                    <div class="floating-animation" style="margin-bottom: 16px;">
                        <svg class="star-icon" style="width: 64px; height: 64px; margin: 0 auto;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <h1 class="welcome-title">
                        Welcome to IEHSAS !
                    </h1>
                    <p class="welcome-subtitle">
                        {{ __("You're successfully logged in and ready to begin your journey.") }}
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
                                Once you've completed watching the safety training video, you can proceed to take the entry test. 
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
                                    Start Entry Test
                                </button>
                                
                                <div style="font-size: 0.875rem; color: #6b7280; display: flex; align-items: center;">
                                    <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Estimated time: 15-20 minutes
                                </div>
                            </div>
                        </div>

                        <!-- Information Cards -->
                        <div class="grid-3">
                            <div class="feature-card blue">
                                <div class="icon-blue" style="margin-bottom: 16px;">
                                    <svg style="width: 40px; height: 40px; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 style="font-weight: bold; color: #1f2937; margin-bottom: 8px;">Safety First</h4>
                                <p style="color: #6b7280; font-size: 0.875rem;">Learn essential safety protocols and procedures</p>
                            </div>
                            
                            <div class="feature-card green">
                                <div class="icon-green" style="margin-bottom: 16px;">
                                    <svg style="width: 40px; height: 40px; margin: 0 auto;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                </div>
                                <h4 style="font-weight: bold; color: #1f2937; margin-bottom: 8px;">Expert Training</h4>
                                <p style="color: #6b7280; font-size: 0.875rem;">Professional guidance from industry experts</p>
                            </div>
                            
                            <div class="feature-card purple">
                                <div class="icon-purple" style="margin-bottom: 16px;">
                                    <svg style="width: 40px; height: 40px; margin: 0 auto;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
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
                    Ready to Start Test!
                `;
            }
        }
        
        function showCompletionMessage() {
            alert('Great! You have completed the safety training video. You can now proceed to the entry test.');
        }
        
        function checkVideoCompletion() {
            if (videoWatched) {
                window.location.href = "{{ url('/entry-tests') }}";
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
</x-app-layout>