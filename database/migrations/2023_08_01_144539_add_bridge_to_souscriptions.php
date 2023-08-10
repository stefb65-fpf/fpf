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
        Schema::table('souscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('personne_id')->nullable()->after('id');
            $table->string('bridge_id')->nullable()->default(null)->after('ref_reglement');
            $table->string('bridge_link')->nullable()->default(null)->after('bridge_id');
            $table->string('monext_token')->nullable()->default(null)->after('bridge_link');
            $table->string('monext_link')->nullable()->default(null)->after('monext_token');
            $table->timestamps();
            $table->foreign('personne_id')->references('id')->on('personnes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('souscriptions', function (Blueprint $table) {
            $table->dropForeign('personne_id');
            $table->dropColumn(['personne_id', 'bridge_id', 'bridge_link', 'monext_token', 'monext_link', 'created_at', 'updated_at']);
        });
    }
};
