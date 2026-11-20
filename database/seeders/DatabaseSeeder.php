<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = collect([
            ['name' => 'Further Plant', 'code' => 'FP'],
            ['name' => 'Sausage Plant', 'code' => 'SP'],
            ['name' => 'Warehouse', 'code' => 'WH'],
        ])->map(fn ($department) => Department::updateOrCreate(
            ['code' => $department['code']],
            ['name' => $department['name']]
        ));

        collect([
            'APD',
            'Tindakan Tidak Aman',
            'Housekeeping',
            'Kondisi Tidak Aman',
            'Lingkungan / Ergonomi',
        ])->each(fn ($name) => Category::updateOrCreate(['name' => $name]));

        // Admin default untuk CRUD Master Data
        User::updateOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Admin K3',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_admin' => true,
            'is_auditor' => false,
            'is_auditee' => false,
        ]);

        // // User yang berperan ganda: bisa masuk sebagai Auditor maupun Auditee
        // $both = User::updateOrCreate([
        //     'username' => 'budi',
        // ], [
        //     'name' => 'Budi (Auditor & Auditee)',
        //     'email' => 'budi@example.com',
        //     'password' => Hash::make('password'),
        //     'is_active' => true,
        //     'is_admin' => false,
        //     'is_auditor' => true,
        //     'is_auditee' => true,
        // ]);
        // $both->departments()->sync([$departments->firstWhere('code', 'PRD')->id]);

        // // Auditor murni
        // User::updateOrCreate([
        //     'username' => 'siti',
        // ], [
        //     'name' => 'Siti (Auditor K3)',
        //     'email' => 'siti@example.com',
        //     'password' => Hash::make('password'),
        //     'is_active' => true,
        //     'is_admin' => false,
        //     'is_auditor' => true,
        //     'is_auditee' => false,
        // ]);

        // // Auditee murni, bertanggung jawab atas 2 departemen
        // $auditee = User::updateOrCreate([
        //     'username' => 'andi',
        // ], [
        //     'name' => 'Andi (PIC Maintenance & Warehouse)',
        //     'email' => 'andi@example.com',
        //     'password' => Hash::make('password'),
        //     'is_active' => true,
        //     'is_admin' => false,
        //     'is_auditor' => false,
        //     'is_auditee' => true,
        // ]);
        // $auditee->departments()->sync([
        //     $departments->firstWhere('code', 'MTC')->id,
        //     $departments->firstWhere('code', 'WHS')->id,
        // ]);
    }
}
