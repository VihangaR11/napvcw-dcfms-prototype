<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'Board Secretary',
            'email' => 'board@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'BS001',
            'role' => 'board_secretary',
            'division' => 'Board Secretariat',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Director General',
            'email' => 'dg@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'DG001',
            'role' => 'director_general',
            'division' => 'Director General',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Legal Director',
            'email' => 'legal.director@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'LD001',
            'role' => 'legal_director',
            'division' => 'Law and Law Enforcement',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Legal Officer',
            'email' => 'legal.officer@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'LO001',
            'role' => 'legal_officer',
            'division' => 'Law and Law Enforcement',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Investigation Officer',
            'email' => 'io@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'IO001',
            'role' => 'investigation_officer',
            'division' => 'Law and Law Enforcement',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Assistant Director - Protection Services',
            'email' => 'protection.director@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'PD001',
            'role' => 'protection_director',
            'division' => 'Protection Services',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Protection Officer',
            'email' => 'protection.officer@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'PO001',
            'role' => 'protection_officer',
            'division' => 'Protection Services',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Police Protection Officer',
            'email' => 'police.protection@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'PPO001',
            'role' => 'police_protection_officer',
            'division' => 'Police Protection',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@napvcw.local',
            'password' => 'password123',
            'employee_number' => 'SYS001',
            'role' => 'system_admin',
            'division' => 'System Administration',
            'is_active' => true,
        ]);

        User::updateOrCreate(
    [
        'employee_number' => 'PPD001',
    ],
    [
        'name' => 'Director - Police Protection',
        'designation' => 'Director - Police Protection',
        'role' => 'police_protection_director',
        'division' => 'Police Protection',

        'password' => Hash::make(
            'password123'
        ),

        'account_status' => 'active',
        'is_active' => true,
    ]
    );
    }
}