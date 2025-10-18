<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Successful - IEHSAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        @keyframes bounce-in {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        .bounce-in {
            animation: bounce-in 0.6s ease-out;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-900 via-blue-900 to-purple-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);"></div>

    <!-- Floating Success Icons -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 text-green-400 opacity-20 float" style="animation-delay: 0s;">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="absolute top-40 right-20 text-blue-400 opacity-20 float" style="animation-delay: 1s;">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="absolute bottom-32 left-20 text-purple-400 opacity-20 float" style="animation-delay: 2s;">
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl text-center" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
            
            <!-- Success Icon -->
            <div class="mb-8" x-show="show" x-transition.duration.600ms>
                <div class="inline-flex items-center justify-center w-32 h-32 bg-green-500 bg-opacity-20 rounded-full bounce-in">
                    <svg class="w-16 h-16 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <!-- Main Content -->
            <div class="glass-effect rounded-2xl p-8 mb-8" x-show="show" x-transition.delay.200ms>
                
                <!-- Success Message -->
                <h1 class="text-4xl font-bold text-white mb-4">Enrollment Successful!</h1>
                <p class="text-green-300 text-xl mb-6">
                    🎉 Congratulations! Your enrollment has been submitted successfully.
                </p>

                <!-- What Happens Next -->
                <div class="bg-blue-500 bg-opacity-20 border border-blue-500 rounded-xl p-6 mb-6">
                    <h2 class="text-blue-300 text-xl font-semibold mb-4">What Happens Next?</h2>
                    <div class="text-left space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                            <div>
                                <p class="text-blue-200 font-medium">Document Review</p>
                                <p class="text-blue-300 text-sm">Our admissions team will review your submitted documents within 3-5 business days.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</div>
                            <div>
                                <p class="text-blue-200 font-medium">Email Notification</p>
                                <p class="text-blue-300 text-sm">You'll receive an email with the review results and any additional requirements.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</div>
                            <div>
                                <p class="text-blue-200 font-medium">Final Enrollment</p>
                                <p class="text-blue-300 text-sm">Once approved, you'll receive enrollment confirmation and next steps for joining IEHSAS.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="bg-yellow-500 bg-opacity-20 border border-yellow-500 rounded-xl p-4 mb-6">
                    <h3 class="text-yellow-300 font-semibold mb-2">📋 Important Information</h3>
                    <ul class="text-yellow-200 text-sm text-left space-y-1">
                        <li>• Keep your email notifications for future reference</li>
                        <li>• If documents are rejected, you can resubmit corrected versions</li>
                        <li>• Contact admissions if you don't hear back within 7 business days</li>
                        <li>• Save your verification details for any future correspondence</li>
                    </ul>
                </div>

                <!-- Contact Information -->
                <div class="bg-purple-500 bg-opacity-20 border border-purple-500 rounded-xl p-4">
                    <h3 class="text-purple-300 font-semibold mb-3">📞 Need Help?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-purple-200 font-medium">Admissions Office</p>
                            <p class="text-purple-300">📧 yasir13abbas96@gmail.com</p>
                            <p class="text-purple-300">📱 +92-XX-XXXXXXXX</p>
                        </div>
                        <div>
                            <p class="text-purple-200 font-medium">Office Hours</p>
                            <p class="text-purple-300">Monday - Friday: 9:00 AM - 5:00 PM</p>
                            <p class="text-purple-300">Saturday: 9:00 AM - 1:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4" x-show="show" x-transition.delay.400ms>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('entry-test.register') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4"/>
                        </svg>
                        Back to Entry Test Portal
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Confirmation
                    </button>
                </div>
                
                <!-- Social Media Celebration -->
                <div class="pt-4">
                    <p class="text-gray-300 text-sm mb-3">Share your achievement!</p>
                    <div class="flex justify-center space-x-4">
                        <button class="text-blue-400 hover:text-blue-300 transition-colors duration-200" 
                                onclick="shareOnTwitter()">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </button>
                        <button class="text-blue-600 hover:text-blue-500 transition-colors duration-200" 
                                onclick="shareOnFacebook()">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>
                        <button class="text-green-500 hover:text-green-400 transition-colors duration-200" 
                                onclick="shareOnWhatsApp()">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-white border-opacity-20">
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} Institute of Engineering, Health Sciences and Applied Sciences (IEHSAS)
                </p>
            </div>
        </div>
    </div>

    <script>
        function shareOnTwitter() {
            const text = "Just completed my enrollment at IEHSAS! Excited to start my journey in engineering and health sciences. 🎓 #IEHSAS #Education #Success";
            const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank', 'width=550,height=420');
        }

        function shareOnFacebook() {
            const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`;
            window.open(url, '_blank', 'width=555,height=400');
        }

        function shareOnWhatsApp() {
            const text = "Just completed my enrollment at IEHSAS! Excited to start my journey in engineering and health sciences. 🎓";
            const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        // Confetti effect (optional enhancement)
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7', '#dda0dd'];
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.top = '-10px';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.opacity = Math.random();
                    confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                    confetti.style.transition = 'all 3s linear';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.zIndex = '9999';
                    
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => {
                        confetti.style.top = '100vh';
                        confetti.style.transform = 'rotate(' + (Math.random() * 360 + 720) + 'deg)';
                    }, 100);
                    
                    setTimeout(() => {
                        confetti.remove();
                    }, 3000);
                }, i * 100);
            }
        }

        // Trigger confetti after page load
        setTimeout(createConfetti, 1000);
    </script>
</body>
</html>