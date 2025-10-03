<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Stack;
use App\Models\Subject;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $stackCount = Stack::count();
        if ($stackCount === 0) {
            $this->command->error('No stacks found! Run StackSeeder first.');
            return;
        }
        $topicCount = Topic::count();
        if ($topicCount === 0) {
            $this->command->error('No Topic found! Run TopicSeeder first.');
            return;
        }
        $subjectCount = Subject::count();
        if ($subjectCount === 0) {
            $this->command->error('No Subject found! Run SubjectSeeder first.');
            return;
        }
        $now = Carbon::now();

        // fetch subject and topic ids once (no DB calls inside loop)
        $subjectIds = Subject::pluck('id')->toArray();
        $topicIds = Topic::pluck('id')->toArray();

        if (empty($subjectIds) || empty($topicIds)) {
            // nothing to seed if subjects/topics are missing
            return;
        }

        $exams = [];

        for ($i = 1; $i <= 200; $i++) {
            $subjectId = $subjectIds[array_rand($subjectIds)];
            $topicId = $topicIds[array_rand($topicIds)];
            Log::info("topicId: " . $topicId);
            Log::info("subjectId: " . $subjectId);
            $exams[] = [
                'title' => "Exam #$i - " . Str::random(5),
                'description' => "Mock exam number $i with realistic difficulty.",
                'stack_id' => rand(1, 9),
                'subject_id' => $subjectId,
                'topic_id' => $topicId,
                'duration_minutes' => rand(20, 90),
                'total_marks' => rand(50, 100),
                'passing_score' => rand(20, 40),
                'max_attempts' => rand(1, 3),
                'price_in_points' => rand(0, 20),
                'is_active' => 1,
                'start_time' => $now,
                'end_time' => $now->copy()->addDays(rand(5, 90)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Exam::truncate();
        // insert in chunks to be safe
        $chunks = array_chunk($exams, 100);
        foreach ($chunks as $chunk) {
            Exam::insert($chunk);
        }
    }
}
