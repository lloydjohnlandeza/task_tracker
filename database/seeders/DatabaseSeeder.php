<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('task_statuses')->insert([
            [
              'status' => 'Complete',
              'color' => '#4CAF50',
            ],
            [
              'status' => 'Pending',
              'color' => '#FFEB3B',
            ],
            [
              'status' => 'Cancel',
              'color' => '#F44336',
            ],
        ]);
        DB::table('users')->insert([
          [
            'name' => 'Sample User',
            'email' => 'sampleuser@gmail.com',
            'password' => bcrypt('qweqweqwe')
          ]
      ]);
    }
}
