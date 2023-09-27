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
            $table->decimal('price', 10, 2)->default(0)->after('level');
            $table->unsignedSmallInteger('places')->default(0)->after('price');
            $table->unsignedSmallInteger('waiting_places')->default(0)->after('places');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('places');
            $table->dropColumn('waiting_places');
        });
    }
};
