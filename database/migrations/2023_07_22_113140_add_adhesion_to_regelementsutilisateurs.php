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
            $table->unsignedTinyInteger('adhesion')->default(0)->comment('0: pas d\'adhésion, 1: adhésion');
            $table->unsignedTinyInteger('abonnement')->default(0)->comment('0: pas d\'abonnement, 1: abonnement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglementsutilisateurs', function (Blueprint $table) {
            $table->dropColumn('adhesion');
            $table->dropColumn('abonnement');
        });
    }
};
