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
        Schema::table('personnes', function (Blueprint $table) {
            $table->unsignedTinyInteger('attente_paiement')->default(0)->after('avoir_formation')->comment('1: paiement en attente');
            $table->string('action_paiement')->nullable()->default(null)->after('attente_paiement')->comment('action à faire après le paiement réussi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnes', function (Blueprint $table) {
            $table->dropColumn('attente_paiement');
            $table->dropColumn('action_paiement');
        });
    }
};
