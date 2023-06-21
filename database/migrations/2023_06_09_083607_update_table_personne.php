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
        Schema::table('personnes', function (Blueprint $table) {
            $table->date('datenaissance')->nullable()->default(null)->after('tva');
            $table->unsignedTinyInteger('news')->default(1)->after('datenaissance');
            $table->date('blacklist_date')->nullable()->default(null)->after('news');
            $table->string('secure_code', 150)->nullable()->default(null)->after('blacklist_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnes', function (Blueprint $table) {
            //
        });
    }
};
