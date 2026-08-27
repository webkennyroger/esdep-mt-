<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `admin_user_id` nasceu para ligar um contato com cargo "Admin" a uma conta
 * administrativa. Com o SSO, qualquer contato pode ter conta de login — o nome
 * passou a mentir. Renomeado para `user_id`: a conta de login deste contato.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('contacts', 'admin_user_id')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('admin_user_id', 'user_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('contacts', 'user_id')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('user_id', 'admin_user_id');
        });
    }
};
