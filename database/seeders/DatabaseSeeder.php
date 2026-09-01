<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->call([
            CoaAccountSeeder::class,
            TaxSettingSeeder::class,
        ]);

        $branch = Branch::create([
            'nama_cabang' => 'Cabang Utama',
            'alamat' => null,
        ]);

        Warehouse::create([
            'branch_id' => $branch->id,
            'nama_gudang' => 'Gudang Utama',
            'is_default' => true,
        ]);
    }
}