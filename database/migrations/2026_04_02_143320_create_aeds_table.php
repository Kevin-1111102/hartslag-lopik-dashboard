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
        $table->string('type');
        $table->string('serienummer')->nullable();
        $table->string('adres');
        $table->string('huisnummer')->nullable();
        $table->string('plaats');
        $table->text('beschrijving')->nullable();
        $table->string('pincode')->nullable();
        $table->string('onderhoudscode')->nullable();
        $table->date('batterij_vervaldatum')->nullable();
        $table->date('elektroden_vervaldatum')->nullable();

        // Simpele versie zonder foreign key (voor nu)
        $table->unsignedBigInteger('user_id')->nullable();
        $table->index('user_id');   // alleen een index, geen strenge koppeling

        // Beheerafspraken
        $table->boolean('shl_beheerder')->default(false);
        $table->boolean('shl_verantwoordelijk_controle')->default(false);
        $table->boolean('shl_hartslagnu_beheer')->default(false);
        $table->boolean('externe_onderhoud')->default(false);

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
