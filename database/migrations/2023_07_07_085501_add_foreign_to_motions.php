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
        Schema::table('motions', function (Blueprint $table) {
            $table->foreign('elections_id')->references('id')->on('elections')->onDelete('cascade');
            $table->foreign('reponses_id')->references('id')->on('reponses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motions', function (Blueprint $table) {
            $table->dropForeign(['elections_id', 'reponses_id']);
        });
    }
};
