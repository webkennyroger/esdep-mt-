<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_announcements', function (Blueprint $table): void {
            $table->foreignId('created_by')->nullable()->change();
            $table->foreignId('created_by_contact_id')->nullable()->after('created_by')->constrained('contacts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_announcements', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('created_by_contact_id');
            $table->foreignId('created_by')->nullable(false)->change();
        });
    }
};
