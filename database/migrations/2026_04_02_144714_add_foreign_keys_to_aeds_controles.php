<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foreign key voor aeds tabel
        Schema::table('aeds', function (Blueprint $table) {
            $table->foreign('user_id', 'fk_aeds_user_id')   // unieke naam
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });

        // Foreign keys voor controles tabel
        Schema::table('controles', function (Blueprint $table) {
            $table->foreign('aed_id', 'fk_controles_aed_id')   // unieke naam
                  ->references('id')
                  ->on('aeds')
                  ->onDelete('cascade');

            $table->foreign('user_id', 'fk_controles_user_id') // unieke naam
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('aeds', function (Blueprint $table) {
            $table->dropForeign('fk_aeds_user_id');
        });

        Schema::table('controles', function (Blueprint $table) {
            $table->dropForeign('fk_controles_aed_id');
            $table->dropForeign('fk_controles_user_id');
        });
    }
};