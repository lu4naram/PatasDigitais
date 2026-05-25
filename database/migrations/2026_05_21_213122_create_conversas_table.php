<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('animal_id')
                ->constrained('animais')
                ->restrictOnDelete();

            $table->foreignId('adotante_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('doador_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversas');
    }
};