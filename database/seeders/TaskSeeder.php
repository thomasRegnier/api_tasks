<?php

namespace Database\Seeders;
use Faker\Factory as Faker;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();
        for($i = 0; $i < 50; $i++){
            $task = new Task;
            $task->body = $faker->word();
            $task->user_id = rand(1, 50);
            $task->completed = rand(0, 1);
            $task->save();
        }
    }
}
