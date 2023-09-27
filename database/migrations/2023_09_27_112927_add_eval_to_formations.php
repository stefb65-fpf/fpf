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
        Schema::table('formations', function (Blueprint $table) {
            $table->decimal('stars', 2, 1)->default(0)->after('new');
            $table->unsignedSmallInteger('reviews')->default(0)->after('stars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn('stars');
            $table->dropColumn('reviews');
        });
    }
};
