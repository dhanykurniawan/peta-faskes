<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\KabupatenKotaSeeder;
use Database\Seeders\KecamatanSeeder;
use Database\Seeders\KelasTempatTidurSeeder;
use Database\Seeders\LayananSeeder;
use Database\Seeders\FasilitasSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AdminUserSeeder::class,
            KelasTempatTidurSeeder::class,
            LayananSeeder::class,
            KabupatenKotaSeeder::class,
            KecamatanSeeder::class, // harus setelah KabupatenKotaSeeder
            FasilitasSeeder::class, // harus setelah wilayah dan layanan
        ]);
    }
}
