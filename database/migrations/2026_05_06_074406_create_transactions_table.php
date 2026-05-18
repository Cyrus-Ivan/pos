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
        Schema::create('transactions', function (Blueprint $table) {
            // Using UUID for local-first sync safety
            $table->ulid('id')->primary();

            $table->string('branch_id');
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')->constrained();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users')->index();

            // Cached total for quick history views
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('payment_type', ['cash', 'online']);

            $table->timestamps();
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->timestamp('synced_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
