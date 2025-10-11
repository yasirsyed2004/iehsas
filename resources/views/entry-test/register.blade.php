{{-- File: resources/views/entry-test/register.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - IEHSAS Entry Test</title>
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

        .form-input {
            transition: all 0.3s ease;
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .id-format {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
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
                        <a href="{{ route('home') }}" class="text-white hover:text-gray-200 transition duration-300">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="min-h-screen flex items-center justify-center px-4 py-8">
            <div class="max-w-4xl mx-auto w-full">
                <!-- Main Card -->
                <div class="glass-effect rounded-3xl p-8 md:p-12 slide-up">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="text-5xl text-blue-500 mb-4">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                            Student Registration
                        </h1>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Please provide your information to register for the {{ $entryTest->title }}
                        </p>
                    </div>

                    <!-- Test Information Summary -->
                    <div class="bg-blue-50 rounded-xl p-6 mb-8">
                        <h3 class="text-lg font-bold text-blue-800 mb-3 text-center">
                            <i class="fas fa-info-circle mr-2"></i>Test Information
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <div class="font-semibold text-blue-800">Duration</div>
                                <div class="text-blue-600">{{ $entryTest->duration_minutes }} minutes</div>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Questions</div>
                                <div class="text-blue-600">{{ $entryTest->total_questions }}</div>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Passing Score</div>
                                <div class="text-blue-600">{{ $entryTest->passing_score }}%</div>
                            </div>
                            <div>
                                <div class="font-semibold text-blue-800">Attempts</div>
                                <div class="text-blue-600">1 per ID</div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form action="{{ route('entry-test.register.submit') }}" method="POST" id="registrationForm">
                        @csrf
                        
                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold text-red-800">Please correct the following errors:</h3>
                                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1"></i>Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="full_name" 
                                       value="{{ old('full_name') }}"
                                       class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('full_name') border-red-500 @enderror"
                                       placeholder="Enter your full name"
                                       required>
                                @error('full_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Father's Name -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-user-tie mr-1"></i>Father's Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="father_name" 
                                       value="{{ old('father_name') }}"
                                       class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('father_name') border-red-500 @enderror"
                                       placeholder="Enter your father's name"
                                       required>
                                @error('father_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-1"></i>Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                       placeholder="your.email@example.com"
                                       required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-1"></i>Contact Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       name="contact_number" 
                                       value="{{ old('contact_number') }}"
                                       class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_number') border-red-500 @enderror"
                                       placeholder="03XX-XXXXXXX"
                                       required
                                       id="contactInput">
                                @error('contact_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt mr-1"></i>Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="date_of_birth" 
                                       value="{{ old('date_of_birth') }}"
                                       max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                                       class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_of_birth') border-red-500 @enderror"
                                       required>
                                @error('date_of_birth')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ID Type -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-id-card mr-1"></i>ID Type <span class="text-red-500">*</span>
                                </label>
                                <select name="id_type" 
                                        id="idTypeSelect"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('id_type') border-red-500 @enderror"
                                        required>
                                    <option value="">Select ID Type</option>
                                    @foreach($idTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('id_type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ID Number -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-hashtag mr-1"></i><span id="idNumberLabel">ID Number</span> <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="id_number" 
                                       value="{{ old('id_number') }}"
                                       class="id-format form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('id_number') border-red-500 @enderror"
                                       placeholder="Please select ID type first"
                                       disabled
                                       required
                                       id="idNumberInput">
                                <p class="text-xs text-gray-500 mt-1" id="formatHint" style="display: none;"></p>
                                @error('id_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nationality -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-flag mr-1"></i>Nationality <span class="text-red-500">*</span>
                                </label>
                                <select name="nationality" 
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nationality') border-red-500 @enderror"
                                        required>
                                    @foreach($nationalities as $nationality)
                                        <option value="{{ $nationality }}" {{ old('nationality', 'Pakistan') == $nationality ? 'selected' : '' }}>
                                            {{ $nationality }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nationality')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-venus-mars mr-1"></i>Gender <span class="text-red-500">*</span>
                                </label>
                                <select name="gender" 
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror"
                                        required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Qualification -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-graduation-cap mr-1"></i>Qualification <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="qualification" 
                                       value="{{ old('qualification') }}"
                                       class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('qualification') border-red-500 @enderror"
                                       placeholder="e.g., Bachelor's, Master's, Intermediate"
                                       required>
                                @error('qualification')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Home Address (Full Width) -->
                        <div class="mt-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-home mr-1"></i>Home Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="home_address" 
                                      rows="3"
                                      maxlength="1000"
                                      class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('home_address') border-red-500 @enderror"
                                      placeholder="Enter your complete home address"
                                      required
                                      id="homeAddressInput">{{ old('home_address') }}</textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-xs text-gray-500">Maximum 1000 characters</p>
                                <p class="text-xs text-gray-500" id="addressCounter">0/1000</p>
                            </div>
                            @error('home_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mt-8">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <label class="flex items-start">
                                    <input type="checkbox" 
                                           name="terms" 
                                           class="mt-1 mr-3" 
                                           required>
                                    <span class="text-sm text-gray-700">
                                        I agree to the <strong>terms and conditions</strong> and confirm that all information provided is accurate. 
                                        I understand that I can only attempt this test once with my ID unless granted special permission by the administration.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-8">
                            <button type="submit" 
                                    id="submitBtn"
                                    class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition duration-300 shadow-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                Complete Registration
                            </button>
                            <p class="text-gray-600 mt-4 text-sm">
                                After registration, you'll be redirected to test instructions
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ID Type Configuration
        const idTypeConfig = {
            cnic: {
                label: 'CNIC Number',
                placeholder: '12345-1234567-1',
                hint: 'Format: 12345-1234567-1',
                maxLength: 15,
                pattern: /^[0-9-]*$/
            },
            passport: {
                label: 'Passport Number',
                placeholder: 'AB1234567',
                hint: 'Format: 2 letters + 7 digits',
                maxLength: 9,
                pattern: /^[A-Z0-9]*$/
            },
            driving_license: {
                label: 'Driving License Number',
                placeholder: 'ABC12345',
                hint: 'Format: 8-15 alphanumeric characters',
                maxLength: 15,
                pattern: /^[A-Z0-9]*$/
            }
        };

        // Elements
        const idTypeSelect = document.getElementById('idTypeSelect');
        const idNumberInput = document.getElementById('idNumberInput');
        const idNumberLabel = document.getElementById('idNumberLabel');
        const formatHint = document.getElementById('formatHint');

        // Handle ID Type Selection
        idTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            
            if (selectedType && idTypeConfig[selectedType]) {
                const config = idTypeConfig[selectedType];
                
                // Enable and configure input
                idNumberInput.disabled = false;
                idNumberInput.placeholder = config.placeholder;
                idNumberInput.maxLength = config.maxLength;
                idNumberInput.value = '';
                
                // Update label and hint
                idNumberLabel.textContent = config.label;
                formatHint.textContent = config.hint;
                formatHint.style.display = 'block';
                
                idNumberInput.focus();
            } else {
                // Disable input
                idNumberInput.disabled = true;
                idNumberInput.placeholder = 'Please select ID type first';
                idNumberInput.value = '';
                
                // Reset label and hide hint
                idNumberLabel.textContent = 'ID Number';
                formatHint.style.display = 'none';
            }
        });

        // Handle ID Number Input
        idNumberInput.addEventListener('input', function() {
            const idType = idTypeSelect.value;
            const config = idTypeConfig[idType];
            
            if (!config) return;
            
            let value = this.value.toUpperCase();
            
            // Apply pattern filtering
            if (config.pattern) {
                value = value.replace(new RegExp('[^' + config.pattern.source.slice(2, -2) + ']', 'g'), '');
            }
            
            // Special formatting for CNIC
            if (idType === 'cnic') {
                const digits = value.replace(/\D/g, '');
                
                if (digits.length <= 5) {
                    value = digits;
                } else if (digits.length <= 12) {
                    value = digits.slice(0, 5) + '-' + digits.slice(5);
                } else if (digits.length <= 13) {
                    value = digits.slice(0, 5) + '-' + digits.slice(5, 12) + '-' + digits.slice(12, 13);
                } else {
                    value = digits.slice(0, 5) + '-' + digits.slice(5, 12) + '-' + digits.slice(12, 13);
                }
            }
            
            this.value = value;
        });

        // Contact Number Formatting
        const contactInput = document.getElementById('contactInput');
        contactInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length > 0 && !value.startsWith('03')) {
                if (value.startsWith('3')) {
                    value = '0' + value;
                } else if (!value.startsWith('0')) {
                    value = '03' + value;
                }
            }
            
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            
            this.value = value;
        });

        // Address Character Counter
        const homeAddressInput = document.getElementById('homeAddressInput');
        const addressCounter = document.getElementById('addressCounter');
        
        homeAddressInput.addEventListener('input', function() {
            const length = this.value.length;
            addressCounter.textContent = `${length}/1000`;
            
            if (length > 950) {
                addressCounter.style.color = '#ffc107';
            } else if (length >= 1000) {
                addressCounter.style.color = '#dc3545';
            } else {
                addressCounter.style.color = '#6b7280';
            }
        });

        // Initialize character counter
        if (homeAddressInput.value) {
            homeAddressInput.dispatchEvent(new Event('input'));
        }

        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const idType = idTypeSelect.value;
            const idNumber = idNumberInput.value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!idType) {
                e.preventDefault();
                alert('Please select an ID type.');
                idTypeSelect.focus();
                return;
            }
            
            if (!idNumber.trim()) {
                e.preventDefault();
                alert('Please enter your ID number.');
                idNumberInput.focus();
                return;
            }
            
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        });

        // Initialize form state
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger ID type change if there's an old value
            if (idTypeSelect.value) {
                idTypeSelect.dispatchEvent(new Event('change'));
            }
        });

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