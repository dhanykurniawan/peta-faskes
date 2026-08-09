<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('adm!n'),
            ],
        );

        $this->command->info('✓ Admin user berhasil dibuat');
    }
}
