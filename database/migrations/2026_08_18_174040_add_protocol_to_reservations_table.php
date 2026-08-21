<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('protocol')->nullable()->unique()->after('id');
        });

        // Backfill existing records
        $reservations = DB::table('reservations')->get();
        foreach ($reservations as $reservation) {
            $year = date('Y', strtotime($reservation->created_at ?? $reservation->date));
            $protocol = sprintf('RES-%s-%04d', $year, $reservation->id);
            DB::table('reservations')->where('id', $reservation->id)->update(['protocol' => $protocol]);
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('protocol');
        });
    }
};
