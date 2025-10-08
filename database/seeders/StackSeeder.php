<?php

namespace Database\Seeders;

use App\Models\Stack;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        // Stack::truncate();


        $stacks = [
            [
                'name' => 'HSC',
                'description' => 'Higher Secondary Certificate level topics.',
                'icon' => null,
                'color' => '#3B82F6',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'SSC',
                'description' => 'Secondary School Certificate level topics.',
                'icon' => null,
                'color' => '#3B82F6',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'University',
                'description' => 'General university admission topics.',
                'icon' => null,
                'color' => '#3B82F6',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Medical',
                'description' => 'Medical (MBBS & Dental) admission topics.',
                'icon' => null,
                'color' => '#EF4444',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Engineering',
                'description' => 'Engineering admission topics.',
                'icon' => null,
                'color' => '#10B981',
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Textile',
                'description' => 'Textile-related admission topics (BUTEX).',
                'icon' => null,
                'color' => '#8B5CF6',
                'sort_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'IELTS',
                'description' => 'International English Language Testing System topics.',
                'icon' => null,
                'color' => '#F59E0B',
                'sort_order' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Software Engineering',
                'description' => 'Software engineering fundamentals and concepts.',
                'icon' => null,
                'color' => '#06B6D4',
                'sort_order' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'PHP & Laravel',
                'description' => 'PHP and Laravel development-related topics.',
                'icon' => null,
                'color' => '#4F46E5',
                'sort_order' => 9,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Stack::insert($stacks);
    }
}
