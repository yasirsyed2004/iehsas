<?php
// File: database/seeders/CourseSeeder.php - FIXED VERSION

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseCategory;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if required tables exist
        $requiredTables = ['course_categories', 'courses', 'course_modules', 'course_lessons'];
        $missingTables = [];
        
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }
        
        if (!empty($missingTables)) {
            $this->command->error('Missing tables: ' . implode(', ', $missingTables));
            $this->command->error('Please run: php artisan migrate first');
            return;
        }

        $this->command->info('🌱 Starting course seeding...');

        // Create Course Categories (using firstOrCreate to avoid duplicates)
        $this->createCategories();

        // Get instructor
        $instructor = $this->getInstructor();
        if (!$instructor) {
            return;
        }

        // Create Sample Courses
        $this->createCourses($instructor);

        $this->command->info('✅ Course seeding completed successfully!');
    }

    private function createCategories()
    {
        $categories = [
            [
                'name' => 'Programming',
                'slug' => 'programming',
                'description' => 'Learn programming languages and software development',
                'icon' => 'fas fa-code',
                'color' => '#3498db',
                'sort_order' => 1
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Frontend and backend web development courses',
                'icon' => 'fas fa-globe',
                'color' => '#e74c3c',
                'sort_order' => 2
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'description' => 'Data analysis, machine learning, and AI courses',
                'icon' => 'fas fa-chart-bar',
                'color' => '#9b59b6',
                'sort_order' => 3
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'iOS and Android app development',
                'icon' => 'fas fa-mobile-alt',
                'color' => '#f39c12',
                'sort_order' => 4
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'Online marketing and social media strategies',
                'icon' => 'fas fa-bullhorn',
                'color' => '#2ecc71',
                'sort_order' => 5
            ]
        ];

        foreach ($categories as $categoryData) {
            // Use firstOrCreate to avoid duplicates based on slug
            $category = CourseCategory::firstOrCreate(
                ['slug' => $categoryData['slug']], // Find by slug
                $categoryData // Create with this data if not found
            );

            if ($category->wasRecentlyCreated) {
                $this->command->info("✅ Created category: {$category->name}");
            } else {
                $this->command->info("📄 Category already exists: {$category->name}");
            }
        }
    }

    private function getInstructor()
    {
        // Try to find admin user first
        $instructor = User::where('role', 'admin')->first();
        
        // If no admin, try to find any user with teacher or admin role
        if (!$instructor) {
            $instructor = User::whereIn('role', ['teacher', 'admin'])->first();
        }
        
        // If still no instructor, try any user
        if (!$instructor) {
            $instructor = User::first();
        }

        if (!$instructor) {
            $this->command->error('❌ No users found. Please create a user first.');
            $this->command->info('💡 Run: php artisan make:user or create a user manually');
            return null;
        }

        $this->command->info("👨‍🏫 Using instructor: {$instructor->name} ({$instructor->email})");
        return $instructor;
    }

    private function createCourses($instructor)
    {
        $courses = [
            [
                'title' => 'Complete Python Programming Bootcamp',
                'slug' => 'complete-python-programming-bootcamp',
                'description' => 'Master Python programming from basics to advanced concepts. Learn data structures, algorithms, web development with Django, and more.',
                'short_description' => 'Comprehensive Python course covering basics to advanced topics',
                'category_slug' => 'programming',
                'level' => 'beginner',
                'status' => 'published',
                'price' => 99.99,
                'discount_price' => 79.99,
                'duration_hours' => 40,
                'max_students' => 100,
                'requires_entry_test' => true,
                'min_entry_test_score' => 60,
                'has_certificate' => true,
                'learning_outcomes' => [
                    'Understand Python syntax and basic programming concepts',
                    'Work with data structures like lists, dictionaries, and sets',
                    'Build web applications using Django framework',
                    'Implement object-oriented programming principles'
                ],
                'requirements' => 'Basic computer skills and willingness to learn',
                'is_featured' => true,
                'modules' => [
                    ['title' => 'Getting Started', 'lessons' => ['Introduction to Programming', 'Setting up Environment', 'Your First Program']],
                    ['title' => 'Basic Concepts', 'lessons' => ['Variables and Data Types', 'Control Structures', 'Functions']],
                    ['title' => 'Advanced Topics', 'lessons' => ['Object-Oriented Programming', 'Error Handling', 'Final Project']]
                ]
            ],
            [
                'title' => 'Modern Web Development with React',
                'slug' => 'modern-web-development-with-react',
                'description' => 'Learn to build modern, responsive web applications using React, JavaScript ES6+, and popular libraries.',
                'short_description' => 'Build modern web apps with React and JavaScript',
                'category_slug' => 'web-development',
                'level' => 'intermediate',
                'status' => 'published',
                'price' => 129.99,
                'duration_hours' => 35,
                'max_students' => 80,
                'requires_entry_test' => false,
                'has_certificate' => true,
                'learning_outcomes' => [
                    'Build dynamic user interfaces with React',
                    'Manage application state with Redux',
                    'Implement modern JavaScript ES6+ features',
                    'Deploy applications to production'
                ],
                'requirements' => 'Basic HTML, CSS, and JavaScript knowledge',
                'is_featured' => true,
                'modules' => [
                    ['title' => 'Foundation', 'lessons' => ['HTML Basics', 'CSS Styling', 'JavaScript Fundamentals']],
                    ['title' => 'React Basics', 'lessons' => ['Components and Props', 'State Management', 'Event Handling']],
                    ['title' => 'Advanced React', 'lessons' => ['Hooks and Context', 'Routing', 'Deployment']]
                ]
            ],
            [
                'title' => 'Data Science with Python and Machine Learning',
                'slug' => 'data-science-python-machine-learning',
                'description' => 'Dive into data science using Python, pandas, numpy, and scikit-learn. Learn to analyze data and build machine learning models.',
                'short_description' => 'Complete data science course with Python and ML',
                'category_slug' => 'data-science',
                'level' => 'advanced',
                'status' => 'published',
                'price' => 149.99,
                'discount_price' => 119.99,
                'duration_hours' => 50,
                'max_students' => 60,
                'requires_entry_test' => true,
                'min_entry_test_score' => 70,
                'has_certificate' => true,
                'learning_outcomes' => [
                    'Analyze and visualize data using pandas and matplotlib',
                    'Build machine learning models with scikit-learn',
                    'Understand statistical concepts and data distributions',
                    'Deploy ML models to production'
                ],
                'requirements' => 'Python programming experience and basic mathematics',
                'is_featured' => false,
                'modules' => [
                    ['title' => 'Data Analysis', 'lessons' => ['Pandas Basics', 'Data Cleaning', 'Data Visualization']],
                    ['title' => 'Statistics', 'lessons' => ['Descriptive Statistics', 'Probability', 'Hypothesis Testing']],
                    ['title' => 'Machine Learning', 'lessons' => ['Supervised Learning', 'Unsupervised Learning', 'Model Evaluation']]
                ]
            ]
        ];

        foreach ($courses as $courseData) {
            $this->createCourse($courseData, $instructor);
        }
    }

    private function createCourse($courseData, $instructor)
    {
        // Get category
        $category = CourseCategory::where('slug', $courseData['category_slug'])->first();
        if (!$category) {
            $this->command->error("❌ Category not found: {$courseData['category_slug']}");
            return;
        }

        // Prepare course data
        $courseAttributes = [
            'title' => $courseData['title'],
            'slug' => $courseData['slug'],
            'description' => $courseData['description'],
            'short_description' => $courseData['short_description'],
            'category_id' => $category->id,
            'instructor_id' => $instructor->id,
            'level' => $courseData['level'],
            'status' => $courseData['status'],
            'price' => $courseData['price'],
            'discount_price' => $courseData['discount_price'] ?? null,
            'duration_hours' => $courseData['duration_hours'],
            'max_students' => $courseData['max_students'] ?? null,
            'requires_entry_test' => $courseData['requires_entry_test'],
            'min_entry_test_score' => $courseData['min_entry_test_score'] ?? null,
            'has_certificate' => $courseData['has_certificate'],
            'learning_outcomes' => $courseData['learning_outcomes'],
            'requirements' => $courseData['requirements'],
            'is_featured' => $courseData['is_featured'],
            'is_active' => true,
            'published_at' => now()
        ];

        // Create or find course
        $course = Course::firstOrCreate(
            ['slug' => $courseData['slug']], // Find by slug
            $courseAttributes // Create with this data if not found
        );

        if ($course->wasRecentlyCreated) {
            $this->command->info("✅ Created course: {$course->title}");
            
            // Create modules and lessons for new course
            $this->createModulesAndLessons($course, $courseData['modules']);
        } else {
            $this->command->info("📄 Course already exists: {$course->title}");
        }
    }

    private function createModulesAndLessons($course, $modules)
    {
        foreach ($modules as $index => $moduleData) {
            $module = CourseModule::firstOrCreate([
                'course_id' => $course->id,
                'title' => $moduleData['title']
            ], [
                'description' => "Learn about {$moduleData['title']} in {$course->title}",
                'order' => $index + 1,
                'duration_minutes' => 60 * count($moduleData['lessons']),
                'is_active' => true
            ]);

            if ($module->wasRecentlyCreated) {
                $this->command->info("  📚 Created module: {$module->title}");
            }

            foreach ($moduleData['lessons'] as $lessonIndex => $lessonTitle) {
                $lesson = CourseLesson::firstOrCreate([
                    'course_module_id' => $module->id,
                    'title' => $lessonTitle
                ], [
                    'description' => "Detailed lesson about {$lessonTitle}",
                    'type' => $lessonIndex === count($moduleData['lessons']) - 1 ? 'assignment' : 'video',
                    'content' => "This lesson covers {$lessonTitle} in detail. Students will learn practical skills and concepts.",
                    'video_url' => $lessonIndex !== count($moduleData['lessons']) - 1 ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                    'duration_minutes' => rand(15, 45),
                    'order' => $lessonIndex + 1,
                    'is_active' => true
                ]);

                if ($lesson->wasRecentlyCreated) {
                    $this->command->info("    📝 Created lesson: {$lesson->title}");
                }
            }
        }
    }
}