<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `reservation_logs` nunca teve Model nem consumidor: o historico de reservas
 * e feito por `activity_logs` (via ActivityLogger), que cobre o mesmo escopo
 * e mais (aprovacao, rejeicao, reabertura).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reservation_logs');
    }

    public function down(): void
    {
        Schema::create('reservation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->json('changes')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'created_at']);
        });
    }
};
