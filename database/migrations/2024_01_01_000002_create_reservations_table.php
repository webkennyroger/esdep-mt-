<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->string('requester_name');
            $table->string('requester_email');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('attendees_count');
            $table->enum('status', ['pending', 'authorized', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->index(['room_id', 'date', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};