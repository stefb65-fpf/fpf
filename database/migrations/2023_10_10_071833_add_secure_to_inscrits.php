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
        Schema::table('inscrits', function (Blueprint $table) {
            $table->string('secure_code')->nullable()->default(null)->after('attente_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscrits', function (Blueprint $table) {
            $table->dropColumn('secure_code');
        });
    }
};
