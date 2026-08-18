<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->timestamp('autorizado_at')->nullable()->after('setor');
            $table->timestamp('last_login_at')->nullable()->after('autorizado_at');
        });

        DB::table('contacts')->where('autorizado', true)->update(['autorizado_at' => now()]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('autorizado');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('autorizado')->default(false)->after('setor');
        });

        DB::table('contacts')->whereNotNull('autorizado_at')->update(['autorizado' => true]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['autorizado_at', 'last_login_at']);
        });
    }
};
