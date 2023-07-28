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
            $table->string('bridge_id')->nullable()->default(null)->after('reference');
            $table->string('bridge_link')->nullable()->default(null)->after('bridge_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglements', function (Blueprint $table) {
            $table->dropColumn('bridge_id');
            $table->dropColumn('bridge_link');
        });
    }
};
