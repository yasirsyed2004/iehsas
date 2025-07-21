<?php
// File: database/seeders/EntryTestSeeder.php

namespace Database\Seeders;

use App\Models\EntryTest;
use App\Models\EntryTestQuestion;
use Illuminate\Database\Seeder;

class EntryTestSeeder extends Seeder
{
    public function run()
    {
        // Create Entry Test
        $entryTest = EntryTest::create([
            'title' => 'General Knowledge Entry Test',
            'description' => 'Basic entry test for course enrollment',
            'duration_minutes' => 25,
            'total_questions' => 20,
            'passing_score' => 60,
            'is_active' => true
        ]);

        // Sample questions with more variety
        $questions = [
            [
                'question_text' => 'What is the capital of Pakistan?',
                'question_type' => 'mcq',
                'options' => ['Karachi', 'Lahore', 'Islamabad', 'Peshawar'],
                'correct_answer' => 'Islamabad',
                'marks' => 5,
                'order' => 1
            ],
            [
                'question_text' => 'Which planet is known as the Red Planet?',
                'question_type' => 'mcq',
                'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                'correct_answer' => 'Mars',
                'marks' => 5,
                'order' => 2
            ],
            [
                'question_text' => 'What is the largest ocean on Earth?',
                'question_type' => 'mcq',
                'options' => ['Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean'],
                'correct_answer' => 'Pacific Ocean',
                'marks' => 5,
                'order' => 3
            ],
            [
                'question_text' => 'Who wrote the famous novel "Pride and Prejudice"?',
                'question_type' => 'mcq',
                'options' => ['Charlotte Bronte', 'Jane Austen', 'Emily Dickinson', 'Virginia Woolf'],
                'correct_answer' => 'Jane Austen',
                'marks' => 5,
                'order' => 4
            ],
            [
                'question_text' => 'What is the chemical symbol for gold?',
                'question_type' => 'mcq',
                'options' => ['Go', 'Gd', 'Au', 'Ag'],
                'correct_answer' => 'Au',
                'marks' => 5,
                'order' => 5
            ],
            [
                'question_text' => 'In which year did World War II end?',
                'question_type' => 'mcq',
                'options' => ['1944', '1945', '1946', '1947'],
                'correct_answer' => '1945',
                'marks' => 5,
                'order' => 6
            ],
            [
                'question_text' => 'What is the smallest prime number?',
                'question_type' => 'mcq',
                'options' => ['0', '1', '2', '3'],
                'correct_answer' => '2',
                'marks' => 5,
                'order' => 7
            ],
            [
                'question_text' => 'Which gas makes up approximately 78% of Earth\'s atmosphere?',
                'question_type' => 'mcq',
                'options' => ['Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Hydrogen'],
                'correct_answer' => 'Nitrogen',
                'marks' => 5,
                'order' => 8
            ],
            [
                'question_text' => 'What is the currency of Japan?',
                'question_type' => 'mcq',
                'options' => ['Yuan', 'Won', 'Yen', 'Ringgit'],
                'correct_answer' => 'Yen',
                'marks' => 5,
                'order' => 9
            ],
            [
                'question_text' => 'Which organ in the human body produces insulin?',
                'question_type' => 'mcq',
                'options' => ['Liver', 'Kidney', 'Pancreas', 'Heart'],
                'correct_answer' => 'Pancreas',
                'marks' => 5,
                'order' => 10
            ],
            [
                'question_text' => 'What is the fastest land animal?',
                'question_type' => 'mcq',
                'options' => ['Lion', 'Cheetah', 'Leopard', 'Tiger'],
                'correct_answer' => 'Cheetah',
                'marks' => 5,
                'order' => 11
            ],
            [
                'question_text' => 'Which programming language is known as the "mother of all languages"?',
                'question_type' => 'mcq',
                'options' => ['FORTRAN', 'COBOL', 'C', 'Assembly'],
                'correct_answer' => 'C',
                'marks' => 5,
                'order' => 12
            ],
            [
                'question_text' => 'What is the hardest natural substance on Earth?',
                'question_type' => 'mcq',
                'options' => ['Gold', 'Iron', 'Diamond', 'Platinum'],
                'correct_answer' => 'Diamond',
                'marks' => 5,
                'order' => 13
            ],
            [
                'question_text' => 'Which country is known as the "Land of the Rising Sun"?',
                'question_type' => 'mcq',
                'options' => ['China', 'Japan', 'South Korea', 'Thailand'],
                'correct_answer' => 'Japan',
                'marks' => 5,
                'order' => 14
            ],
            [
                'question_text' => 'What is the boiling point of water at sea level?',
                'question_type' => 'mcq',
                'options' => ['90°C', '95°C', '100°C', '105°C'],
                'correct_answer' => '100°C',
                'marks' => 5,
                'order' => 15
            ],
            [
                'question_text' => 'Who painted the Mona Lisa?',
                'question_type' => 'mcq',
                'options' => ['Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo'],
                'correct_answer' => 'Leonardo da Vinci',
                'marks' => 5,
                'order' => 16
            ],
            [
                'question_text' => 'What is the largest mammal in the world?',
                'question_type' => 'mcq',
                'options' => ['African Elephant', 'Blue Whale', 'Giraffe', 'Hippopotamus'],
                'correct_answer' => 'Blue Whale',
                'marks' => 5,
                'order' => 17
            ],
            [
                'question_text' => 'Which vitamin is produced when skin is exposed to sunlight?',
                'question_type' => 'mcq',
                'options' => ['Vitamin A', 'Vitamin B', 'Vitamin C', 'Vitamin D'],
                'correct_answer' => 'Vitamin D',
                'marks' => 5,
                'order' => 18
            ],
            [
                'question_text' => 'What is the study of earthquakes called?',
                'question_type' => 'mcq',
                'options' => ['Geology', 'Seismology', 'Meteorology', 'Astronomy'],
                'correct_answer' => 'Seismology',
                'marks' => 5,
                'order' => 19
            ],
            [
                'question_text' => 'Which is the longest river in the world?',
                'question_type' => 'mcq',
                'options' => ['Amazon River', 'Nile River', 'Mississippi River', 'Yangtze River'],
                'correct_answer' => 'Nile River',
                'marks' => 5,
                'order' => 20
            ]
        ];

        foreach ($questions as $questionData) {
            $entryTest->questions()->create($questionData);
        }
    }
}