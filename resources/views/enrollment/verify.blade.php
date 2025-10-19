{{-- File: resources/views/enrollment/verify.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Verification - IEHSAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 0%, transparent 50%);"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">IEHSAS</h1>
                <p class="text-blue-200 text-lg">Enrollment Verification</p>
            </div>

            <!-- Verification Form -->
            <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                {{-- Show ALL errors for debugging --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500 bg-opacity-20 border border-red-500 rounded-lg">
                        <h4 class="text-red-300 font-semibold mb-2">Errors:</h4>
                        <ul class="text-red-300 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-500 bg-opacity-20 border border-green-500 rounded-lg">
                        <p class="text-green-300 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('enrollment.verify.submit') }}" x-data="enrollmentForm" @submit="prepareSubmission()">
                    @csrf

                    <!-- Hidden field for clean ID number (what gets submitted) -->
                    <input type="hidden" name="id_number" x-model="cleanIdNumber">

                    <!-- Instructions -->
                    <div class="mb-6 p-4 bg-blue-500 bg-opacity-20 border border-blue-500 rounded-lg">
                        <h3 class="text-blue-300 font-semibold mb-2">📧 Check Your Email</h3>
                        <p class="text-blue-200 text-sm">
                            Enter your ID details and the 4-digit verification code sent to your email to access the enrollment form.
                        </p>
                    </div>

                    <!-- ID Type Selection -->
                    <div class="mb-6">
                        <label class="block text-white text-sm font-medium mb-3">ID Type</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="id_type" value="cnic" x-model="idType" class="hidden peer" required>
                                <div class="p-3 text-center border border-white border-opacity-30 rounded-lg peer-checked:bg-blue-500 peer-checked:bg-opacity-30 peer-checked:border-blue-400 transition-all duration-200">
                                    <div class="text-white text-sm font-medium">CNIC</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="id_type" value="passport" x-model="idType" class="hidden peer" required>
                                <div class="p-3 text-center border border-white border-opacity-30 rounded-lg peer-checked:bg-blue-500 peer-checked:bg-opacity-30 peer-checked:border-blue-400 transition-all duration-200">
                                    <div class="text-white text-sm font-medium">Passport</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="id_type" value="driving_license" x-model="idType" class="hidden peer" required>
                                <div class="p-3 text-center border border-white border-opacity-30 rounded-lg peer-checked:bg-blue-500 peer-checked:bg-opacity-30 peer-checked:border-blue-400 transition-all duration-200">
                                    <div class="text-white text-sm font-medium">License</div>
                                </div>
                            </label>
                        </div>
                        @error('id_type')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ID Number Input (Display Only) -->
                    <div class="mb-6" x-show="idType">
                        <label class="block text-white text-sm font-medium mb-2" x-text="getIdLabel()"></label>
                        <input 
                            type="text" 
                            x-model="displayIdNumber"
                            @input="formatIdNumber()"
                            :placeholder="getIdPlaceholder()"
                            :maxlength="getIdMaxLength()"
                            class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                            required
                        >
                        <p class="text-gray-300 text-xs mt-1" x-text="getIdHint()"></p>
                        @error('id_number')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Verification Code -->
                    <div class="mb-6">
                        <label class="block text-white text-sm font-medium mb-2">Verification Code</label>
                        <input 
                            type="text" 
                            name="verification_code" 
                            placeholder="Enter 4-digit code"
                            maxlength="4"
                            class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 text-center text-2xl tracking-widest"
                            required
                            x-model="verificationCode"
                            @input="verificationCode = verificationCode.replace(/[^0-9]/g, '').substring(0, 4)"
                        >
                        @error('verification_code')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Debug Info (remove this after testing) -->
                    <div class="mb-4 p-3 bg-gray-800 bg-opacity-50 rounded text-white text-xs" x-show="idType">
                        <strong>DEBUG:</strong><br>
                        Display: <span x-text="displayIdNumber"></span><br>
                        Clean: <span x-text="cleanIdNumber"></span><br>
                        Code: <span x-text="verificationCode"></span>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 transform hover:scale-105"
                        :disabled="!canSubmit()"
                        :class="canSubmit() ? '' : 'opacity-50 cursor-not-allowed'"
                    >
                        Verify & Continue
                    </button>
                </form>

                <!-- Help Section -->
                <div class="mt-8 pt-6 border-t border-white border-opacity-20">
                    <div class="text-center">
                        <p class="text-gray-300 text-sm mb-2">Need help?</p>
                        <p class="text-blue-300 text-sm">
                            📧 Contact: <a href="mailto:yasir13abbas96@gmail.com" class="underline hover:text-blue-200">yasir13abbas96@gmail.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('enrollmentForm', () => ({
                idType: '{{ old('id_type') }}',
                displayIdNumber: '{{ old('id_number') }}', // What user sees (with dashes)
                cleanIdNumber: '',  // What gets submitted (without dashes)
                verificationCode: '{{ old('verification_code') }}',

                getIdLabel() {
                    switch(this.idType) {
                        case 'cnic': return 'CNIC Number';
                        case 'passport': return 'Passport Number';
                        case 'driving_license': return 'Driving License Number';
                        default: return 'ID Number';
                    }
                },

                getIdPlaceholder() {
                    switch(this.idType) {
                        case 'cnic': return '37203-2524192-3';
                        case 'passport': return 'AB1234567';
                        case 'driving_license': return 'ABC123456789';
                        default: return '';
                    }
                },

                getIdMaxLength() {
                    switch(this.idType) {
                        case 'cnic': return 15; // With dashes
                        case 'passport': return 20;
                        case 'driving_license': return 20;
                        default: return 20;
                    }
                },

                getIdHint() {
                    switch(this.idType) {
                        case 'cnic': return 'Format: 12345-1234567-1 (13 digits with dashes)';
                        case 'passport': return 'Passport number from your passport document';
                        case 'driving_license': return 'License number from your driving license';
                        default: return '';
                    }
                },

                formatIdNumber() {
                    if (this.idType === 'cnic') {
                        // Remove all non-digits first
                        let cleaned = this.displayIdNumber.replace(/[^0-9]/g, '');
                        
                        // Store clean version for submission (just digits)
                        this.cleanIdNumber = cleaned.substring(0, 13);
                        
                        // Format for display with dashes: XXXXX-XXXXXXX-X
                        let formatted = cleaned;
                        if (cleaned.length >= 5) {
                            formatted = cleaned.substring(0, 5) + '-' + cleaned.substring(5);
                        }
                        if (cleaned.length >= 12) {
                            formatted = formatted.substring(0, 13) + '-' + cleaned.substring(12, 13);
                        }
                        
                        // Update display
                        this.displayIdNumber = formatted.substring(0, 15);
                    } else {
                        // For passport and license, just uppercase and limit length
                        this.displayIdNumber = this.displayIdNumber.toUpperCase().substring(0, 20);
                        this.cleanIdNumber = this.displayIdNumber;
                    }
                },

                canSubmit() {
                    return this.idType && 
                           this.cleanIdNumber && 
                           this.cleanIdNumber.trim().length > 0 && 
                           this.verificationCode && 
                           this.verificationCode.length === 4;
                },

                prepareSubmission() {
                    // Ensure clean ID number is set before form submission
                    if (this.idType === 'cnic') {
                        this.cleanIdNumber = this.displayIdNumber.replace(/[^0-9]/g, '').substring(0, 13);
                    } else {
                        this.cleanIdNumber = this.displayIdNumber;
                    }
                    console.log('Submitting:', {
                        idType: this.idType,
                        cleanId: this.cleanIdNumber,
                        code: this.verificationCode
                    });
                }
            }))
        });
    </script>
</body>
</html>