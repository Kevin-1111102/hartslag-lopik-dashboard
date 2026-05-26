<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('type'); // batterij|elektroden
            $table->foreignId('aed_id')->constrained()->cascadeOnDelete();

            $table->text('bericht');
            $table->date('datum');

            $table->boolean('gelezen')->default(false);

            $table->timestamps();

            $table->index(['type', 'aed_id']);
            $table->index(['gelezen', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

