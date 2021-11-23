<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $admin= User::create([
            'name'=> 'admin',
            'username'=>'admin',
            'email'=>'admin@gmail.com',
            'password'=> bcrypt('Admin123')
        ]);


        $admin->assignRole('admin');
    }
}
