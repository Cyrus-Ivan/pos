<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users');

            $table->string('branch_id');
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches');

            $table->string('photo_path')->nullable();
            $table->timestamp('photo_taken_at')->nullable(); // when photo was taken
            $table->enum('type', ['in', 'out']);

            $table->timestamps(); // record stored time 
            $table->index(['user_id', 'photo_taken_at', 'photo_path']);
            $table->index(['branch_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_audits');
    }
};
