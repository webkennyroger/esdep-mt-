<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_contact_id')->nullable()->constrained('contacts')->cascadeOnDelete();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('color', 7)->default('#64748b');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_groups');
    }
};
