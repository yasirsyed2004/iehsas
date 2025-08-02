<?php
// File: app/Models/User.php - ADD these relationships to the existing User model

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'student_id',
        'phone',
        'date_of_birth',
        'gender',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'date_of_birth' => 'date'
        ];
    }

    // EXISTING RELATIONSHIPS (Entry Test related)
    public function entryTestAttempts(): HasMany
    {
        return $this->hasMany(EntryTestAttempt::class);
    }

    // NEW COURSE-RELATED RELATIONSHIPS
    
    /**
     * Courses taught by this user (if instructor/admin)
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Course enrollments for this user
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Active course enrollments
     */
    public function activeCourseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class)
            ->whereIn('status', ['enrolled', 'active']);
    }

    /**
     * Completed course enrollments
     */
    public function completedCourseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class)
            ->where('status', 'completed');
    }

    /**
     * Course progress records
     */
    public function courseProgress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    /**
     * Course reviews written by this user
     */
    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    /**
     * Get courses the user is enrolled in
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
            ->withPivot(['status', 'enrolled_at', 'started_at', 'completed_at', 'progress_percentage'])
            ->withTimestamps();
    }

    // HELPER METHODS

    /**
     * Check if user can teach courses
     */
    public function canTeach(): bool
    {
        return in_array($this->role, ['admin', 'teacher']);
    }

    /**
     * Check if user is enrolled in a specific course
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->courseEnrollments()
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Get user's progress in a specific course
     */
    public function getCourseProgress(Course $course): float
    {
        $enrollment = $this->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    /**
     * Check if user has completed a specific course
     */
    public function hasCompletedCourse(Course $course): bool
    {
        return $this->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get user's certificate for a course
     */
    public function getCourseCertificate(Course $course)
    {
        // Implementation depends on your certificate system
        if ($this->hasCompletedCourse($course) && $course->has_certificate) {
            // Return certificate data or generate certificate
            return [
                'course_title' => $course->title,
                'student_name' => $this->name,
                'completion_date' => $this->courseEnrollments()
                    ->where('course_id', $course->id)
                    ->first()
                    ->completed_at,
                'certificate_id' => 'CERT-' . $course->id . '-' . $this->id
            ];
        }
        return null;
    }

    /**
     * Check if user has passed entry test (for course enrollment)
     */
    public function hasPassedEntryTest($minimumScore = 60): bool
    {
        return $this->entryTestAttempts()
            ->where('status', 'completed')
            ->where('percentage', '>=', $minimumScore)
            ->exists();
    }

    /**
     * Get user's best entry test score
     */
    public function getBestEntryTestScore(): float
    {
        return $this->entryTestAttempts()
            ->where('status', 'completed')
            ->max('percentage') ?? 0;
    }

    // SCOPES

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for instructors
     */
    public function scopeInstructors($query)
    {
        return $query->whereIn('role', ['admin', 'teacher']);
    }

    /**
     * Scope for students
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }
}