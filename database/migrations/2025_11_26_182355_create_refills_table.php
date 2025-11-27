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
        Schema::create('refills', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
    $table->string('type'); // Walk-in, Delivery
    $table->integer('gallons_dispensed');
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refills');
    }
};
