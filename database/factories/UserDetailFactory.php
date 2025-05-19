<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserDetailFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			'first_name' => 'Master',
			'middle_name' => 'Sudo',
			'last_name' => 'User',
			'email' => 'superuser@superuser',
			'phone' => '0000000000',
			'address' => 'root',
			'date_of_birth' => now(),
			'gender' => true,
			'profile_image' => 'default.jpg',
			
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

			'user_id' => 1
        ];
    }
}
