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
        Schema::create('flagged_customer_reasons', function (Blueprint $table) {
            $table->id();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->text('reason');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flagged_customer_reasons');
    }
};
