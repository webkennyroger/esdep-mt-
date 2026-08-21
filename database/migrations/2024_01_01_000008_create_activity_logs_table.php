<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // created, updated, approved, rejected, deleted, etc.
            $table->text('description'); // Descricao legivel da acao
            $table->string('subject_type')->nullable(); // Reservation, User, Room, etc.
            $table->unsignedBigInteger('subject_id')->nullable(); // ID do objeto afetado
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Quem fez a acao
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->json('changes')->nullable(); // Detalhes das mudancas (antes/depois)
            $table->json('metadata')->nullable(); // Informacoes adicionais
            $table->timestamps();

            // Indices para performance
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
