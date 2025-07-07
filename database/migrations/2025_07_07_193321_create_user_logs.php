<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();

            $table->string('description', 45);

			$table->datetime('created_at');
            $table->string('created_at_timezone', 200);
            $table->integer('created_by_user_id');
            $table->string('created_by_username', 45);
            $table->string('created_by_user_type', 45);
            $table->datetime('updated_at');
            $table->string('updated_at_timezone', 200);
            $table->integer('updated_by_user_id');
            $table->string('updated_by_username', 45);
            $table->string('updated_by_user_type', 45);
            $table->boolean('enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
