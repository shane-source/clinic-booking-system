<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'doctor',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            ['name' => 'Admin', 'password' => bcrypt('password123')]
        );
        $admin->assignRole('admin');

        echo "Done! Roles created.\n";
        echo "Admin: admin@clinic.com / password123\n";
    }
}