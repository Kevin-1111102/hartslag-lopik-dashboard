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
        Schema::table('aeds', function (Blueprint $table) {
            $table->enum('status', ['actief', 'archief', 'verwijderd'])->default('actief')->after('externe_onderhoud');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aeds', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('status');
        });
    }
};

