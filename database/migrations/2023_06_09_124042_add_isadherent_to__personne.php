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
            $table->unsignedTinyInteger('is_adherent')->default(0)->after('secure_code')
                ->comment("0: n'a jamais été adhérent, 1: adhésion en cours sur au moins une carte, 2: a été adhérent mais plus d'adhésion en cours");
            $table->unsignedTinyInteger('is_abonne')->default(0)->after('is_adherent')
                ->comment("0: n'a jamais été abonné, 1: abonnement en cours de validité, 2: a été abonné mais pas d'abonnement en cours");
            $table->unsignedTinyInteger('is_administratif')->default(0)->after('is_abonne');
            $table->unsignedTinyInteger('is_formateur')->default(0)->after('is_administratif')->comment('1 si la personne physique ou morale est formateur FPF');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnes', function (Blueprint $table) {
            $table->dropColumn('is_adherent');
            $table->dropColumn('is_abonne');
            $table->dropColumn('is_administratif');
            $table->dropColumn('is_formateur');
        });
    }
};
