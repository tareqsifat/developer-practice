<?php

namespace Database\Seeders;

use App\Models\Stack;
use App\Models\Subject;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Validate prerequisites
        $stackCount = Stack::count();
        if ($stackCount === 0) {
            $this->command->error('No stacks found! Run StackSeeder first.');
            return;
        }

        $topicCount = Topic::count();
        if ($topicCount === 0) {
            $this->command->error('No topics found! Run TopicSeeder first.');
            return;
        }

        $now = Carbon::now();
        $inserts = [];
        $sortOrder = 0;

        // Define subjects with their stack and topic relationships
        $subjects = [
            // HSC (Stack ID: 1)
            ['stack_id' => 1, 'topic_id' => 2, 'name' => 'HSC English 1st Paper', 'description' => 'HSC English first paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 2, 'name' => 'HSC English 2nd Paper', 'description' => 'HSC English second paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 1, 'name' => 'HSC Bangla 1st Paper', 'description' => 'HSC Bangla first paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 1, 'name' => 'HSC Bangla 2nd Paper', 'description' => 'HSC Bangla second paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 3, 'name' => 'HSC Higher Math 1st Paper', 'description' => 'HSC Higher Mathematics first paper'],
            ['stack_id' => 1, 'topic_id' => 3, 'name' => 'HSC Higher Math 2nd Paper', 'description' => 'HSC Higher Mathematics second paper'],
            ['stack_id' => 1, 'topic_id' => 7, 'name' => 'HSC Physics 1st Paper', 'description' => 'HSC Physics first paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 7, 'name' => 'HSC Physics 2nd Paper', 'description' => 'HSC Physics second paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 7, 'name' => 'HSC Chemistry 1st Paper', 'description' => 'HSC Chemistry first paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 7, 'name' => 'HSC Chemistry 2nd Paper', 'description' => 'HSC Chemistry second paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 7, 'name' => 'HSC Biology 1st Paper', 'description' => 'HSC Biology first paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 7, 'name' => 'HSC Biology 2nd Paper', 'description' => 'HSC Biology second paper syllabus'],
            ['stack_id' => 1, 'topic_id' => 8, 'name' => 'HSC ICT', 'description' => 'HSC Information & Communication Technology'],

            // SSC (Stack ID: 2)
            ['stack_id' => 2, 'topic_id' => 2, 'name' => 'SSC English', 'description' => 'SSC English syllabus'],
            ['stack_id' => 2, 'topic_id' => 1, 'name' => 'SSC Bangla', 'description' => 'SSC Bangla syllabus'],
            ['stack_id' => 2, 'topic_id' => 3, 'name' => 'SSC Mathematics', 'description' => 'SSC General Mathematics'],
            ['stack_id' => 2, 'topic_id' => 7, 'name' => 'SSC General Science', 'description' => 'SSC General Science syllabus'],
            ['stack_id' => 2, 'topic_id' => 5, 'name' => 'SSC Bangladesh & Global Studies', 'description' => 'SSC Bangladesh and Global Studies'],
            ['stack_id' => 2, 'topic_id' => 8, 'name' => 'SSC ICT', 'description' => 'SSC Information & Communication Technology'],

            // University (Stack ID: 3)
            ['stack_id' => 3, 'topic_id' => 1, 'name' => 'Bangla Grammar', 'description' => 'University admission Bangla grammar'],
            ['stack_id' => 3, 'topic_id' => 2, 'name' => 'English Grammar', 'description' => 'University admission English grammar'],
            ['stack_id' => 3, 'topic_id' => 3, 'name' => 'Algebra', 'description' => 'University admission algebra topics'],
            ['stack_id' => 3, 'topic_id' => 4, 'name' => 'Geometry', 'description' => 'University admission geometry'],
            ['stack_id' => 3, 'topic_id' => 4, 'name' => 'Trigonometry', 'description' => 'University admission trigonometry'],
            ['stack_id' => 3, 'topic_id' => 5, 'name' => 'Bangladesh Affairs', 'description' => 'Bangladesh history, culture, and current affairs'],
            ['stack_id' => 3, 'topic_id' => 6, 'name' => 'International Affairs', 'description' => 'Global politics and economy'],
            ['stack_id' => 3, 'topic_id' => 7, 'name' => 'General Science', 'description' => 'Physics, Chemistry, Biology basics'],
            ['stack_id' => 3, 'topic_id' => 8, 'name' => 'ICT Fundamentals', 'description' => 'Basic computer and ICT concepts'],
            ['stack_id' => 3, 'topic_id' => 9, 'name' => 'Analytical Ability', 'description' => 'Critical reasoning and problem solving'],
            ['stack_id' => 3, 'topic_id' => 10, 'name' => 'Current Affairs', 'description' => 'Latest national and international updates'],

            // Medical (Stack ID: 4)
            ['stack_id' => 4, 'topic_id' => 7, 'name' => 'Medical Physics', 'description' => 'Physics for medical admission'],
            ['stack_id' => 4, 'topic_id' => 7, 'name' => 'Medical Chemistry', 'description' => 'Chemistry for medical admission'],
            ['stack_id' => 4, 'topic_id' => 7, 'name' => 'Medical Biology', 'description' => 'Biology for MBBS & Dental admission'],
            ['stack_id' => 4, 'topic_id' => 2, 'name' => 'Medical English', 'description' => 'English for medical admission'],
            ['stack_id' => 4, 'topic_id' => 5, 'name' => 'Medical General Knowledge', 'description' => 'GK for medical admission'],

            // Engineering (Stack ID: 5)
            ['stack_id' => 5, 'topic_id' => 7, 'name' => 'Engineering Physics', 'description' => 'Physics for engineering admission'],
            ['stack_id' => 5, 'topic_id' => 7, 'name' => 'Engineering Chemistry', 'description' => 'Chemistry for engineering admission'],
            ['stack_id' => 5, 'topic_id' => 3, 'name' => 'Engineering Math', 'description' => 'Higher mathematics for engineering'],
            ['stack_id' => 5, 'topic_id' => 2, 'name' => 'Engineering English', 'description' => 'English for engineering admission'],

            // Textile (Stack ID: 6)
            ['stack_id' => 6, 'topic_id' => 7, 'name' => 'Textile Physics', 'description' => 'Physics for BUTEX admission'],
            ['stack_id' => 6, 'topic_id' => 7, 'name' => 'Textile Chemistry', 'description' => 'Chemistry for BUTEX admission'],
            ['stack_id' => 6, 'topic_id' => 3, 'name' => 'Textile Math', 'description' => 'Mathematics for textile admission'],
            ['stack_id' => 6, 'topic_id' => 2, 'name' => 'Textile English', 'description' => 'English for BUTEX admission'],
            ['stack_id' => 6, 'topic_id' => 5, 'name' => 'Textile General Knowledge', 'description' => 'GK for textile admission'],

            // IELTS (Stack ID: 7)
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Listening', 'description' => 'IELTS listening skills and practice'],
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Reading', 'description' => 'IELTS reading comprehension'],
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Writing Task 1', 'description' => 'IELTS academic writing task 1'],
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Writing Task 2', 'description' => 'IELTS essay writing task 2'],
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Speaking', 'description' => 'IELTS speaking test preparation'],
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Grammar', 'description' => 'Advanced English grammar for IELTS'],
            ['stack_id' => 7, 'topic_id' => 2, 'name' => 'IELTS Vocabulary', 'description' => 'Essential vocabulary for IELTS'],

            // Software Engineering (Stack ID: 8)
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Data Structures', 'description' => 'Fundamental data structures and algorithms'],
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Algorithms', 'description' => 'Algorithm design and analysis'],
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Object-Oriented Programming', 'description' => 'OOP concepts and principles'],
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Database Management', 'description' => 'Database design and SQL'],
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Web Development Basics', 'description' => 'HTML, CSS, JavaScript fundamentals'],
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Software Design Patterns', 'description' => 'Common design patterns in software'],
            ['stack_id' => 8, 'topic_id' => 8, 'name' => 'Version Control (Git)', 'description' => 'Git and version control systems'],
            ['stack_id' => 8, 'topic_id' => 9, 'name' => 'Problem Solving', 'description' => 'Computational thinking and problem solving'],

            // PHP & Laravel (Stack ID: 9)
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'PHP Fundamentals', 'description' => 'Core PHP programming concepts'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel Basics', 'description' => 'Introduction to Laravel framework'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel Eloquent ORM', 'description' => 'Database interactions with Eloquent'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel Routing & Controllers', 'description' => 'Request handling in Laravel'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel Blade Templates', 'description' => 'Templating engine in Laravel'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel Authentication', 'description' => 'User authentication and authorization'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel API Development', 'description' => 'Building RESTful APIs with Laravel'],
            ['stack_id' => 9, 'topic_id' => 8, 'name' => 'Laravel Testing', 'description' => 'Unit and feature testing in Laravel'],
        ];

        // Prepare inserts with timestamps and sort order
        foreach ($subjects as $subject) {
            $inserts[] = array_merge($subject, [
                'is_active' => 1,
                'sort_order' => $sortOrder++,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Clear existing data and insert new subjects
        // Subject::truncate();

        // Bulk insert all subjects (chunk if necessary for very large datasets)
        foreach (array_chunk($inserts, 100) as $chunk) {
            Subject::insert($chunk);
        }

        $this->command->info('Successfully seeded ' . count($inserts) . ' subjects!');
    }
}
