<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
                                    'id' => 1,
                                    'username' => 'admin',
                                    'first_name' => 'Ing Pedro Castro',
                                    'last_name' => 'SuperAdmin',
                                    'email' => 'admin@mail.com',
                                    'password' => Hash::make('1047387862'),
                                    'status' => 1,
                                ]);
    }
}
