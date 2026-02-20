<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('pwyw_enabled')->default(false)->after('rsvp_capacity');
            $table->decimal('pwyw_amount_1', 10, 2)->nullable()->after('pwyw_enabled');
            $table->decimal('pwyw_amount_2', 10, 2)->nullable()->after('pwyw_amount_1');
            $table->decimal('pwyw_amount_3', 10, 2)->nullable()->after('pwyw_amount_2');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['pwyw_enabled', 'pwyw_amount_1', 'pwyw_amount_2', 'pwyw_amount_3']);
        });
    }
};
