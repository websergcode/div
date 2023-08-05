<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeRole = Role::create(['name' => 'employee']);

        $employee = User::create([
            'name' => 'employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
        ]);

        $employee->roles()->attach($employeeRole);

        User::factory(9)->create();
    }
}
