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
        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(0)->comment('0: session en attente, 1: session confirmée par le gestionnaire, 2: mails de tenue de session envoyés, 3: session terminée, 99: session annulée');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
