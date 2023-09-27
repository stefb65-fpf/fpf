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
        Schema::create('formateurs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personne_id');
            $table->string('title')->nullable()->default(null);
            $table->string('image')->nullable()->default(null);
            $table->longText('cv')->nullable()->default(null);
            $table->string('website')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('cascade');
        });

        Schema::create('formation_formateur', function (Blueprint $table) {
            $table->unsignedBigInteger('formation_id');
            $table->unsignedBigInteger('formateur_id');
            $table->foreign('formation_id')->references('id')->on('formations')->onDelete('cascade');
            $table->foreign('formateur_id')->references('id')->on('formateurs')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_formateur');
        Schema::dropIfExists('formateurs');
    }
};
