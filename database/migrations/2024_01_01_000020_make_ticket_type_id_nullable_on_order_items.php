<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->unsignedBigInteger('ticket_type_id')->nullable()->change();
            $table->foreign('ticket_type_id')
                ->references('id')->on('ticket_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->unsignedBigInteger('ticket_type_id')->nullable(false)->change();
            $table->foreign('ticket_type_id')
                ->references('id')->on('ticket_types')
                ->cascadeOnDelete();
        });
    }
};
