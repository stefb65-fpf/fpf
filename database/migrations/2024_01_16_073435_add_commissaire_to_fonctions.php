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
        Schema::table('fonctions', function (Blueprint $table) {
            $table->unsignedTinyInteger('commissaire')->default(0)->after('instance')->comment('0: non; 1: national; 2: régional; 3 responsable compétitions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fonctions', function (Blueprint $table) {
            $table->dropColumn('commissaire');
        });
    }
};
