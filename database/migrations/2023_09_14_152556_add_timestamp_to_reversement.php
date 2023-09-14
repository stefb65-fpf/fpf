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
        Schema::table('reversements', function (Blueprint $table) {
            $table->timestamps();
//            $table->foreign('urs_id')->references('id')->on('urs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reversements', function (Blueprint $table) {
            $table->dropTimestamps();
//            $table->dropForeign('urs_id');
        });
    }
};
