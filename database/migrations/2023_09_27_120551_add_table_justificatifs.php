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
        Schema::create('justificatifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('personne_id');
            $table->unsignedTinyInteger('type')->default(0)->comment('0: justificatifs; 1: facture');
            $table->string('name');
            $table->timestamps();
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justificatifs');
    }
};
