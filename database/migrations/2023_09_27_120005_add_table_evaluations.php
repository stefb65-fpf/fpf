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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('personne_id');
            $table->unsignedBigInteger('evaluationsitem_id');
            $table->decimal('stars', 2, 1)->default(0);
            $table->text('comment')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('cascade');
            $table->foreign('evaluationsitem_id')->references('id')->on('evaluationsitems')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
