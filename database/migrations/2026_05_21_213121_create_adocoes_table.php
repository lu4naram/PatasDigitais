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
        Schema::create('adocoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('adotante_id')->constrained('users');
            $table->foreignId('doador_id')->constrained('users');
            $table->foreignId('animal_id')->constrained('animais');

            $table->enum('status', ['pendente', 'aprovada', 'recusada', 'cancelada']);
            $table->text('mensagem')->nullable();
            $table->dateTime('data_adocao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adocoes');
    }
};
