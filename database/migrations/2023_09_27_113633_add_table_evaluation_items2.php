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
        Schema::create('evaluationsitems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluationstheme_id');
            $table->string('name');
            $table->unsignedTinyInteger('type')->default(1)->comment('0: texte; 1: note');
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();
            $table->foreign('evaluationstheme_id')->references('id')->on('evaluationsthemes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluationsitems');
    }
};
