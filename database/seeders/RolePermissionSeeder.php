<?php
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Doctor;
 
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $doctorRole  = Role::firstOrCreate(['name' => 'doctor',  'guard_name' => 'web']);
        $patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
 
        // Create test admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            ['name' => 'Admin User', 'password' => 'password123']
        );
        $admin->assignRole('admin');
 
        // Create test clinic
        $clinic = Clinic::firstOrCreate(
            ['name' => 'MedCare Downtown'],
            ['address' => '123 Main St', 'city' => 'New York',
             'phone' => '555-0100', 'is_active' => true]
        );
 
        // Create test doctor user
        $doctorUser = User::firstOrCreate(
            ['email' => 'doctor@clinic.com'],
            ['name' => 'Dr. Sarah Smith', 'password' => 'password123']
        );
        $doctorUser->assignRole('doctor');
 
        // Create doctor profile
        Doctor::firstOrCreate(
            ['user_id' => $doctorUser->id],
            ['clinic_id' => $clinic->id, 'specialty' => 'General Practice',
             'bio' => 'Experienced GP with 10 years practice.', 'is_available' => true]
        );
 
        // Create test patient
        $patient = User::firstOrCreate(
            ['email' => 'patient@clinic.com'],
            ['name' => 'John Patient', 'password' => 'password123']
        );
        $patient->assignRole('patient');
 
        $this->command->info('Roles, users and test data created!');
        $this->command->info('Admin:   admin@clinic.com   / password123');
        $this->command->info('Doctor:  doctor@clinic.com  / password123');
        $this->command->info('Patient: patient@clinic.com / password123');
    }
}

