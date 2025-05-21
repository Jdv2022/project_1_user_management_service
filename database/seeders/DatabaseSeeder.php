<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDetail;
use App\Models\UserRole;
use App\Models\UserDetailUserRole;
use App\Models\UserDepartment;

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
            UserRole::insert(
				[
					[
						'type_1' => 'Admin',
						'description' => 'Highest level in the hierarchy with full access',
						'level' => '1',
						'status' => true,
						'created_at' => now(),
						'created_at_timezone' => '+08:00',
						'created_by_user_id' => 0,
						'created_by_username' => 'factory',
						'created_by_user_type' => 'dev',
						'updated_at' => now(),
						'updated_at_timezone' => '+08:00',
						'updated_by_user_id' => 0,
						'updated_by_username' => 'factory',
						'updated_by_user_type' => 'dev',
					],
					[
						'type_1' => 'Manager',
						'description' => 'Responsible for managing team operations',
						'level' => '2',
						'status' => true,
						'created_at' => now(),
						'created_at_timezone' => '+08:00',
						'created_by_user_id' => 0,
						'created_by_username' => 'factory',
						'created_by_user_type' => 'dev',
						'updated_at' => now(),
						'updated_at_timezone' => '+08:00',
						'updated_by_user_id' => 0,
						'updated_by_username' => 'factory',
						'updated_by_user_type' => 'dev',
					],
					[
						'type_1' => 'Supervisor',
						'description' => 'Oversees day-to-day activities of employees',
						'level' => '3',
						'status' => true,
						'created_at' => now(),
						'created_at_timezone' => '+08:00',
						'created_by_user_id' => 0,
						'created_by_username' => 'factory',
						'created_by_user_type' => 'dev',
						'updated_at' => now(),
						'updated_at_timezone' => '+08:00',
						'updated_by_user_id' => 0,
						'updated_by_username' => 'factory',
						'updated_by_user_type' => 'dev',
					],
					[
						'type_1' => 'User',
						'description' => 'Basic access with limited permissions',
						'level' => '4',
						'status' => true,
						'created_at' => now(),
						'created_at_timezone' => '+08:00',
						'created_by_user_id' => 0,
						'created_by_username' => 'factory',
						'created_by_user_type' => 'dev',
						'updated_at' => now(),
						'updated_at_timezone' => '+08:00',
						'updated_by_user_id' => 0,
						'updated_by_username' => 'factory',
						'updated_by_user_type' => 'dev',
					],
				]
			);
            $this->command->info("Initial UserRole detail populated!");
        } else {
            $this->command->info("Initial UserRole detail already exists!");
        }

		if (!UserDepartment::exists()) {
            UserDepartment::factory()->create();
            $this->command->info("Initial UserDepartment detail populated!");
        } else {
            $this->command->info("Initial UserDepartment detail already exists!");
        }

        if (!UserDetailUserRole::exists()) {
            UserDetailUserRole::factory()->create();
            $this->command->info("Initial UserDetailUserRole detail populated!");
        } else {
            $this->command->info("Initial UserDetailUserRole detail already exists!");
        }
    }
}
