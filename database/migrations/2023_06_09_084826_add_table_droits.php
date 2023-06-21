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
        Schema::create('droits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });

        Schema::create('droit_utilisateur', function (Blueprint $table) {
            $table->unsignedBigInteger('droit_id');
            $table->unsignedInteger('utilisateur_id');
            $table->foreign('droit_id')->references('id')->on('droits')->onDelete('cascade');
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });

        Schema::create('droit_fonction', function (Blueprint $table) {
            $table->unsignedBigInteger('droit_id');
            $table->unsignedSmallInteger('fonction_id');
            $table->foreign('droit_id')->references('id')->on('droits')->onDelete('cascade');
            $table->foreign('fonction_id')->references('id')->on('fonctions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('droits');
    }
};
