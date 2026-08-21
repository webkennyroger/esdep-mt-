<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_groups', function (Blueprint $table): void {
            $table->foreignId('owner_contact_id')->nullable()->after('id')->constrained('contacts')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0)->index();
        });
        foreach (['contacts', 'rooms', 'reservations'] as $tableName) {
            Schema::table($tableName, fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0)->index());
        }
        Schema::table('contact_contact_group', fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0));
    }

    public function down(): void
    {
        Schema::table('contact_contact_group', fn (Blueprint $table) => $table->dropColumn('sort_order'));
        foreach (['contacts', 'rooms', 'reservations'] as $tableName) {
            Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn('sort_order'));
        }
        Schema::table('contact_groups', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('owner_contact_id');
            $table->dropColumn('sort_order');
        });
    }
};
