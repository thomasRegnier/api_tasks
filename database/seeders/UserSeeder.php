<?php

namespace Database\Seeders;
use Faker\Factory as Faker;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
            $user = new User;
            $user->name = $faker->firstName();
            $user->email = $faker->email();
            $user->password = Hash::make("moimoi");
            $user->save();
        }
    }
};
