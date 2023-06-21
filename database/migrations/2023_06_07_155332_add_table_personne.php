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
        Schema::create('personnes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->default(1)->comment('1: particulier; 2: organisation');
            $table->string('organisation')->nullable()->default(null);
            $table->string('nom')->nullable()->default(null);
            $table->string('prenom')->nullable()->default(null);
            $table->unsignedTinyInteger('sexe')->default(0)->comment('0: homme; 1: femme');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('erreur_init_email')->default(0);
            $table->unsignedTinyInteger('email_confirmed')->default(0)->comment('0 : init; 1: email confirmé');
            $table->string('phone_mobile')->nullable()->default(null);
            $table->string('siret', 20)->nullable()->default(null);
            $table->string('tva', 25)->nullable()->default(null);
            $table->decimal('avoir_formation')->default(0);
            $table->timestamps();
        });

        Schema::create('adresse_personne', function (Blueprint $table) {
           $table->unsignedInteger('adresse_id');
           $table->unsignedBigInteger('personne_id');
           $table->unsignedTinyInteger('defaut')->default(0)->comment('1: adresse par défaut - facturation; 2: adresse livraison');
           $table->foreign('adresse_id')->references('id')->on('adresses')->onDelete('cascade');
           $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adresse_personne');
        Schema::dropIfExists('personnes');
    }
};
