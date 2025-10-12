<?php

namespace Database\Seeders;

use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();
        $topics = [
            ['name' => 'Bangla Grammar', 'description' => 'University admission Bangla grammar basics'],
            ['name' => 'English Grammar', 'description' => 'Parts of speech, tense, narration'],
            ['name' => 'Higher Math Algebra', 'description' => 'HSC & admission algebra topics'],
            ['name' => 'Geometry & Trigonometry', 'description' => 'University admission math'],
            ['name' => 'Bangladesh Affairs', 'description' => 'BCS Bangladesh Affairs syllabus'],
            ['name' => 'International Affairs', 'description' => 'Global politics & economy'],
            ['name' => 'General Science', 'description' => 'Physics, Chemistry basics'],
            ['name' => 'ICT Fundamentals', 'description' => 'BCS ICT core topics'],
            ['name' => 'Analytical Ability', 'description' => 'Critical reasoning & puzzle'],
            ['name' => 'Current Affairs', 'description' => 'Latest national & international updates'],
        ];

        foreach ($topics as &$t) {
            $t['is_active'] = $t['is_active'] ?? 1;
            $t['sort_order'] = $t['sort_order'] ?? 0;
            $t['created_at'] = $now;
            $t['updated_at'] = $now;
        }
        unset($t);

        // Topic::truncate();
        // bulk insert
        Topic::insert($topics);
    }
}
