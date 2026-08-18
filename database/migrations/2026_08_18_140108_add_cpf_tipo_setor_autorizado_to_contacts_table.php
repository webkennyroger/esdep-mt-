<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('cpf')->nullable()->after('email');
            $table->string('tipo')->nullable()->after('cpf');
            $table->string('setor')->nullable()->after('tipo');
            $table->boolean('autorizado')->default(false)->after('setor');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'tipo', 'setor', 'autorizado']);
        });
    }
};
