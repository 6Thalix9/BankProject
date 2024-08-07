<?php

namespace Database\Seeders;

use App\Models\Transition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transition::factory()->count(100)->create();
    }
}
