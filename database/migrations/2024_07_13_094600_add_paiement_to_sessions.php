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
        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedTinyInteger('paiement_status')->default(0)->comment('0: en attente, 1: validé');
            $table->unsignedTinyInteger('attente_paiement')->default(0)->comment('1 : paiement en attente');
            $table->string('bridge_id')->nullable()->default(null);
            $table->string('bridge_link')->nullable()->default(null);
            $table->string('monext_token')->nullable()->default(null);
            $table->string('monext_link')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('paiement_status');
            $table->dropColumn('attente_paiement');
            $table->dropColumn('bridge_id');
            $table->dropColumn('bridge_link');
            $table->dropColumn('monext_token');
            $table->dropColumn('monext_link');
        });
    }
};
