<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index('status');
            $table->index('date');
            $table->index('requester_email');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['date']);
            $table->dropIndex(['requester_email']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
