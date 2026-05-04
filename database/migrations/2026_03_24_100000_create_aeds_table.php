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
        Schema::create('aeds', function (Blueprint $table) {
            $table->id();
            $table->string('eigenaar');
            $table->string('contactpersoon')->nullable();
            $table->string('aed_type');
            $table->string('serienummer')->nullable();
            $table->string('adres');
            $table->string('huisnummer');
            $table->string('plaats');
            $table->text('beschrijving')->nullable();
            $table->string('security')->nullable();
            $table->text('pincode')->nullable();
            $table->text('onderhoudscode')->nullable();
            $table->string('serienummer_kast')->nullable();
            $table->string('serienummer_aed')->nullable();
            $table->date('batterij_vervaldatum')->nullable();
            $table->date('elektroden_vervaldatum')->nullable();
            $table->string('lokaal_contactpersoon')->nullable();
            $table->text('opmerkingen')->nullable();
            $table->enum('status', ['actief', 'inactief', 'vervangen'])->default('actief');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aeds');
    }
};
