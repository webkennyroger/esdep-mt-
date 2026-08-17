<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('coffee_break')->default(false);
            $table->boolean('sonorizacao')->default(false);
            $table->boolean('transmissao')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['coffee_break', 'sonorizacao', 'transmissao']);
        });
    }
};