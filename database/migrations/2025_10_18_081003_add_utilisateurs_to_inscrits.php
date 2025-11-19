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
        Schema::table('inscrits', function (Blueprint $table) {
            $table->unsignedInteger('utilisateur_id')->nullable()->default(null);
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscrits', function (Blueprint $table) {
            $table->dropForeign('inscrits_utilisateur_id_foreign');
            $table->dropColumn('utilisateur_id');
        });
    }
};
