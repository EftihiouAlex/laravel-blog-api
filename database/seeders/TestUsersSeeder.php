<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class TestUsersSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void {
        User::create(['name'=> 'John Doe', 'email' => 'theDoe@gmail.com', 'password' => '123456']);
        User::create(['name'=> 'Maximus', 'email' => 'maximus1990@gmail.com', 'password' => '123456']);
        User::create(['name'=> 'TheDoo', 'email' => 'whosthedoo@gmail.com', 'password' => '123456']);

    }
}