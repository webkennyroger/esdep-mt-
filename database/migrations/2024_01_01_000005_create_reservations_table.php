<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('protocol')->nullable()->unique();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->text('reject_reason')->nullable();
            $table->string('requester_name');
            $table->string('requester_email')->index();
            $table->date('date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('attendees_count');
            $table->enum('status', ['pending', 'authorized', 'rejected'])->default('pending')->index();
            $table->boolean('coffee_break')->default(false);
            $table->boolean('sonorizacao')->default(false);
            $table->boolean('transmissao')->default(false);
            $table->boolean('projetor')->default(false);
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->string('google_event_id')->nullable()->index();
            $table->text('google_meet_url')->nullable();
            $table->string('google_sync_status')->nullable()->index();
            $table->text('google_sync_error')->nullable();
            $table->timestamp('google_synced_at')->nullable();
            $table->string('status_notification_sent_for')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->index(['room_id', 'date', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
