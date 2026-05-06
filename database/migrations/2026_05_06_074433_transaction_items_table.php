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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->foreignUuid('transaction_id')
                ->references('id')
                ->on('transactions');

            $table->foreignId('item_id')
                ->references('id')
                ->on('items')
                ->constrained();

            // Financial data at the time of sale
            $table->integer('quantity'); // Can be negative for cancelled/refund
            $table->decimal('cost', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);

            // cancelled = item was returned sealed , refund = item was damaged/broken
            $table->enum('type', ['sale', 'cancelled', 'refund'])->default('sale')->index();

            $table->timestamps();
            $table->primary(['transaction_id', 'item_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
