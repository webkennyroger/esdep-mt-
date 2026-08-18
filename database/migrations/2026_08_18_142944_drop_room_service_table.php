<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('room_service');
    }

    public function down(): void
    {
        Schema::create('room_service', function ($table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->enum('service_type', ['coffee_break', 'sonorizacao', 'transmissao']);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }
};
