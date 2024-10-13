<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $this->call(RoleSeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(GovernorateSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(ReservationStateSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(PropertyTypeSeeder::class);
        $this->call(ReservationTypeSeeder::class);
    }
}
