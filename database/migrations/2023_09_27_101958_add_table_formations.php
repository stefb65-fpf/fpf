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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('shortDesc')->nullable()->default(null);
            $table->longText('longDesc')->nullable()->default(null);
            $table->longText('program')->nullable()->default(null);
            $table->unsignedBigInteger('categories_formation_id');
            $table->unsignedTinyInteger('type')->default(0)->comment('0: à distance; 1: présentiel; 2: les deux');
            $table->string('location')->nullable()->default(null);
            $table->unsignedTinyInteger('level')->default(0)->comment('0: débutant, 1: intermédiaire, 2: confirmé');
            $table->string('duration')->nullable()->default(null);
            $table->unsignedTinyInteger('new')->default(0);
            $table->timestamps();
            $table->foreign('categories_formation_id')->references('id')->on('categories_formations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
