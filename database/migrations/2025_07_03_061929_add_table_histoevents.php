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
        Schema::create('histoevents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('info')->nullable()->default(null);
            $table->unsignedBigInteger('personne_id')->default(null)->nullable();
            $table->unsignedInteger('utilisateur_id')->default(null)->nullable();
            $table->unsignedBigInteger('club_id')->default(null)->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('set null');
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histoevents');
    }
};
