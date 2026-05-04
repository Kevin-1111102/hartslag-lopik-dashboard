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
        Schema::create('aed_beheerafspraken', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aed_id')->constrained('aeds')->onDelete('cascade');
            $table->boolean('is_beheerder')->default(false);
            $table->boolean('voert_controles_uit')->default(false);
            $table->boolean('beheert_in_hartslagnu')->default(false);
            $table->boolean('extern_onderhoud')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aed_beheerafspraken');
    }
};
