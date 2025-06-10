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
        Schema::table('flagged_customer_reasons', function (Blueprint $table) {
             $table->unsignedBigInteger('action_by')->nullable()->after('reason');
        $table->string('action_type')->default('flagged')->after('action_by');

        // Optional: add foreign key if users table exists
        $table->foreign('action_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flagged_customer_reasons', function (Blueprint $table) {
            //
        });
    }
};
