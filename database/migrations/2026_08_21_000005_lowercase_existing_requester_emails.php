<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('reservations')->update([
            'requester_email' => DB::raw('LOWER(TRIM(requester_email))'),
        ]);
    }

    public function down(): void
    {
        // Irreversível: a caixa original do e-mail não é preservada.
    }
};
