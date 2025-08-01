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
        Schema::table('reglementsutilisateurs', function (Blueprint $table) {
            $table->unsignedTinyInteger('reversement_a_faire')
                ->default(1)
                ->comment('Indique si un reversement est à faire pour ce règlement utilisateur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglementsutilisateurs', function (Blueprint $table) {
            $table->dropColumn('reversement_a_faire');
        });
    }
};
