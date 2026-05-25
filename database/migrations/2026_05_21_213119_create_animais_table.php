<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animais', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('sexo', ['macho', 'femea']);
            $table->text('sobre')->nullable();
            $table->integer('idade')->nullable();
            $table->boolean('castracao');
            $table->boolean('adotado');
            $table->foreignId('especie_id')->constrained('especie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animais');
    }
};
