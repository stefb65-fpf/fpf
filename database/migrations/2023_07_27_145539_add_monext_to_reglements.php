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
        Schema::table('reglements', function (Blueprint $table) {
            $table->string('monext_token')->nullable()->default(null)->after('bridge_link');
            $table->string('monext_link')->nullable()->default(null)->after('monext_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglements', function (Blueprint $table) {
            $table->dropColumn('monext_token');
            $table->dropColumn('monext_link');
        });
    }
};
