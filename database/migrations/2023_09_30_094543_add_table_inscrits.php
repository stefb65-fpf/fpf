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
        Schema::create('inscrits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('personne_id');
            $table->unsignedTinyInteger('status')->default(0)->comment('0: en attente, 1: validé');
            $table->unsignedTinyInteger('attente')->default(0)->comment("0: directement inscrit, 1: inscrit en liste d'attente et non payé");
            $table->unsignedTinyInteger('attente_paiement')->default(0)->comment('1 : paiement en attente');
            $table->string('bridge_id')->nullable()->default(null);
            $table->string('bridge_link')->nullable()->default(null);
            $table->string('monext_token')->nullable()->default(null);
            $table->string('monext_link')->nullable()->default(null);
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
        Schema::dropIfExists('inscrits');
    }
};
