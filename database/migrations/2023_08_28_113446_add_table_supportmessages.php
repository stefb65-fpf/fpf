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
        Schema::create('supportmessages', function (Blueprint $table) {
            $table->id();
            $table->string('provenance')->default('BASE EN LIGNE');
            $table->string('email', 100);
            $table->string('identifiant', 15)->nullable()->default(null);
            $table->string('objet')->nullable()->default(null);
            $table->text('contenu')->nullable()->default(null);
            $table->unsignedTinyInteger('statut')->default(0)->comment('0: non traité, 1: pris en charge, 2: traité');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supportmessages');
    }
};
