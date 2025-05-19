<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
			$table->string('middle_name');
			$table->string('last_name');
			$table->string('email');
			$table->string('phone');
			$table->string('address');
			$table->date('date_of_birth');
			$table->string('gender');
			$table->string('profile_image');

			$table->datetime('created_at');
            $table->string('created_at_timezone', 10)->nullable();
            $table->integer('created_by_user_id')->nullable();
            $table->string('created_by_username', 45)->nullable();
            $table->string('created_by_user_type', 45)->nullable();
            $table->datetime('updated_at');
            $table->string('updated_at_timezone', 10)->nullable();
            $table->integer('updated_by_user_id')->nullable();
            $table->string('updated_by_username', 45)->nullable();
            $table->string('updated_by_user_type', 45)->nullable();
            $table->boolean('enabled')->default(true);

			$table->unsignedBigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
