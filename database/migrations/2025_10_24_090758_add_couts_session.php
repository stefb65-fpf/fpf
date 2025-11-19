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
            $table->float('frais_deplacement')->default(0)->comment('frais de déplacement du formateur, à demander pour chaque session');
            $table->float('pec_fpf')->default(0)->comment('la prise en charge éventuelle par la FPF');
            $table->float('reste_a_charge')->default(0)->comment('reste à charge pour la structure organisatrice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('frais_deplacement');
            $table->dropColumn('pec_fpf');
            $table->dropColumn('reste_structure');
        });
    }
};
