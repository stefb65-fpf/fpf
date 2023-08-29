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
        Schema::table('supportmessages', function (Blueprint $table) {
            $table->longText('answer')->nullable()->default(null)->after('contenu');
            $table->string('answer_name', 100)->nullable()->default(null)->after('answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supportmessages', function (Blueprint $table) {
            $table->dropColumn('answer');
            $table->dropColumn('answer_name');
        });
    }
};
