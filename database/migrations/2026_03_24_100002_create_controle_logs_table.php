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
        Schema::create('controle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aed_id')->constrained('aeds')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('datum');
            $table->text('bevindingen')->nullable();
            $table->boolean('storing')->default(false);
            $table->text('bijzonderheden')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controle_logs');
    }
};
