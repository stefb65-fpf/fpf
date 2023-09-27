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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formation_id');
            $table->unsignedBigInteger('club_id')->nullable()->default(null);
            $table->unsignedTinyInteger('ur_id')->nullable()->default(null);
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedSmallInteger('places')->default(0);
            $table->unsignedSmallInteger('waiting_places')->default(0);
            $table->date('start_date')->nullable()->default(null);
            $table->date('end_date')->nullable()->default(null);
            $table->unsignedTinyInteger('type')->default(0)->comment('0: à distance; 1: présentiel; 2: les deux');
            $table->string('location')->nullable()->default(null);
            $table->unsignedTinyInteger('invoice_status')->default(0)->comment('0: non facturée, 1: facture transmise, 2: facture payée');
            $table->timestamps();
            $table->foreign('formation_id')->references('id')->on('formations')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('ur_id')->references('id')->on('urs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
