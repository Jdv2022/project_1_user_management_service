<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRole>
 */
class UserRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Admin',
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
        ];
    }
}
