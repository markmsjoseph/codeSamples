<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\JobPosting::factory(10)->create();
        \App\Models\Company::factory(10)->create();
        
        \App\Models\Skill::factory()->create([
            'title' => 'Node',
        ]);
        \App\Models\Skill::factory()->create([
            'title' => 'PHP',
        ]);
        \App\Models\Skill::factory()->create([
            'title' => 'Javascript',
        ]);
        \App\Models\Skill::factory()->create([
            'title' => 'Laravel',
        ]);
        \App\Models\Skill::factory()->create([
            'title' => 'React',
        ]);
        \App\Models\Skill::factory()->create([
            'title' => 'Java',
        ]);
        \App\Models\Skill::factory()->create([
            'title' => 'C++',
        ]);

    }
}
