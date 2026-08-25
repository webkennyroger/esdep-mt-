<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_groups', function (Blueprint $table): void {
            $table->string('notification_email')->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('contact_groups', function (Blueprint $table): void {
            $table->dropColumn('notification_email');
        });
    }
};
