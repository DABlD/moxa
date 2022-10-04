<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        // $this->call(RhuSeeder::class);
        // $this->call(MedicineSeeder::class);
        // $this->call(BhcSeeder::class);
        // $this->call(CategorySeeder::class);
        
        $this->call(TransactionTypeSeeder::class);
        $this->call(ThemeSeeder::class);
        // $this->call(AlertSeeder::class);
    }
}
