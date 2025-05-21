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
        Schema::create('user_detail_user_departments', function (Blueprint $table) {
            $table->id();
            
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

			$table->foreignId('user_detail_id')->constrained('user_details')->cascadeOnDelete();
			$table->foreignId('user_deparmtent_id')->constrained('user_departments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_detail_user_departments');
    }
};
