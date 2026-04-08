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
    Schema::create('controles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('aed_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained();
        $table->date('controle_datum');
        $table->enum('status_aed', ['in_order', 'storing', 'niet_gecontroleerd']);
        $table->enum('status_kast', ['in_order', 'beschadigd', 'niet_toegankelijk', 'overig'])->nullable();
        $table->text('opmerkingen')->nullable();
        $table->boolean('actie_nodig')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};
