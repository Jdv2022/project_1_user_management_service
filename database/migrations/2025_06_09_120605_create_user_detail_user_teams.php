<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('user_detail_user_teams', function (Blueprint $table) {
            $table->id();
            
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

			$table->foreignId('user_detail_id')->constrained('user_details')->cascadeOnDelete();
			$table->foreignId('user_team_id')->constrained('user_teams')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_detail_user_teams');
    }

};
