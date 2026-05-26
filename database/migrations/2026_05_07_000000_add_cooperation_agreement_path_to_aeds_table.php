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
            $table->string('cooperation_agreement_path')->nullable()->after('opmerkingen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aeds', function (Blueprint $table) {
            $table->dropColumn('cooperation_agreement_path');
        });
    }
};

