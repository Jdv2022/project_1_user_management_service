<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserDetailUserRole;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->userDetailSeeder();
    }

    private function userDetailSeeder() {
        if (!UserDetail::exists()) {
            UserDetail::factory()->create();
            $this->command->info("Initial UserDetail detail populated!");
        } else {
            $this->command->info("Initial UserDetail detail already exists!");
        }

        if (!UserRole::exists()) {
            UserRole::factory()->create();
            $this->command->info("Initial UserRole detail populated!");
        } else {
            $this->command->info("Initial UserRole detail already exists!");
        }

        if (!UserDetailUserRole::exists()) {
            UserDetailUserRole::factory()->create();
            $this->command->info("Initial UserDetailUserRole detail populated!");
        } else {
            $this->command->info("Initial UserDetailUserRole detail already exists!");
        }
    }
}
