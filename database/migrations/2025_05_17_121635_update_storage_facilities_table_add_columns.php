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
        Schema::table('storage_facilities', function (Blueprint $table) {
        $table->string('name')->after('id');
        $table->string('address')->nullable()->after('name');
        $table->foreignId('storage_company_id')
              ->after('address')
              ->constrained()
              ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_facilities', function (Blueprint $table) {
            $table->dropColumn(['name', 'address']);
            $table->dropForeign(['storage_company_id']);
            $table->dropColumn('storage_company_id');
        });
    }
};
