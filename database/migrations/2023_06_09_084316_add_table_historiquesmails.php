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
        Schema::create('historiquemails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personne_id');
            $table->unsignedInteger('utilisateur_id')->nullable()->default(null);
            $table->string('destinataire');
            $table->string('titre');
            $table->longText('contenu');
            $table->timestamps();
            $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('cascade');
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiquemails');
    }
};
