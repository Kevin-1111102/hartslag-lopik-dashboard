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
            $table->string('photo_path')->nullable()->after('cooperation_agreement_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aeds', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};

